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

namespace Core\Shared\Domain\Event;

use Core\Shared\Domain\AggregateRoot;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class DomainEventMediator
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function publish(AggregateRoot $aggregateRoot): void
    {
        foreach ($aggregateRoot->getUncommittedEvents() as $event) {
            $aggregateRoot->markEventAsDispatched($event);
            $this->dispatcher->dispatch($event);
        }
    }

    public function publishIntegrationEvents(AggregateRoot $aggregateRoot): void
    {
        foreach ($aggregateRoot->events as $event) {
            $integrationEvent = $event->integrationEvent();
            if ($integrationEvent !== null) {
                $this->dispatcher->dispatch($integrationEvent);
            }
        }

        $aggregateRoot->clearEvents();
    }
}
