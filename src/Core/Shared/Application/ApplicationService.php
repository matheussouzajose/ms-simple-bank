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

namespace Core\Shared\Application;

use Core\Shared\Domain\Event\DomainEventMediator;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Throwable;

final readonly class ApplicationService
{
    public function __construct(
        private UnitOfWorkInterface $uow,
        private DomainEventMediator $domainEventMediator,
    ) {
    }

    public function start(): void
    {
        $this->uow->start();
    }

    public function finish(): void
    {
        $aggregateRoots = $this->uow->getAggregates();
        foreach ($aggregateRoots as $aggregateRoot) {
            $this->domainEventMediator->publish($aggregateRoot);
        }

        $this->uow->commit();

        foreach ($aggregateRoots as $aggregateRoot) {
            $this->domainEventMediator->publishIntegrationEvents($aggregateRoot);
        }
    }

    public function fail(): void
    {
        $this->uow->rollback();
    }

    /**
     * @throws Throwable
     */
    public function run(callable $callback): mixed
    {
        $this->start();

        try {
            $result = $callback();
            $this->finish();
            return $result;
        } catch (Throwable $e) {
            $this->fail();
            throw $e;
        }
    }
}
