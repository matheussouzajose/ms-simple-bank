<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Core\Shared\Infra\Persistence\Eloquent;

use Core\Shared\Domain\Entity;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Hyperf\Database\ConnectionInterface;
use Hyperf\DbConnection\Db;
use RuntimeException;
use Throwable;

class UnitOfWorkEloquent implements UnitOfWorkInterface
{
    protected ConnectionInterface $connection;

    protected $transaction;

    /**
     * @var array<string, Entity>
     */
    private array $entities = [];

    public function __construct()
    {
        $this->connection = Db::connection();
    }

    public function start(): void
    {
        if (! $this->transaction) {
            $this->transaction = $this->connection;
            $this->transaction->beginTransaction();
        }
    }

    public function commit(): void
    {
        $this->validateTransaction();
        $this->transaction->commit();
        $this->transaction = null;
    }

    public function rollback(): void
    {
        $this->validateTransaction();
        $this->transaction->rollBack();
        $this->transaction = null;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @throws Throwable
     */
    public function do(callable $workFn)
    {
        $isAutoTransaction = false;

        try {
            if ($this->transaction) {
                $result = $workFn($this);
                $this->transaction = null;
                return $result;
            }

            return $this->connection->transaction(function () use ($workFn, &$isAutoTransaction) {
                $isAutoTransaction = true;
                $this->transaction = $this->connection;
                $result = $workFn($this);
                $this->transaction = null;
                return $result;
            });
        } catch (Throwable $e) {
            if (! $isAutoTransaction) {
                $this->transaction?->rollBack();
            }
            $this->transaction = null;
            throw $e;
        }
    }

    public function addEntity(Entity $entity): void
    {
        $this->entities[spl_object_hash($entity)] = $entity;
    }

    public function getAggregates(): array
    {
        return array_values($this->entities);
    }

    private function validateTransaction(): void
    {
        if (! $this->transaction) {
            throw new RuntimeException('No transaction started.');
        }
    }
}
