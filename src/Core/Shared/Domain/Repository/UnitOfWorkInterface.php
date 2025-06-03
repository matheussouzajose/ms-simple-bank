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

namespace Core\Shared\Domain\Repository;

use Core\Shared\Domain\AggregateRoot;

interface UnitOfWorkInterface
{
    public function start(): void;

    public function commit(): void;

    public function rollback(): void;

    public function getTransaction();

    public function do(callable $workFn);

    public function addEntity(AggregateRoot $entity): void;

    /**
     * @return AggregateRoot[]
     */
    public function getAggregates(): array;
}
