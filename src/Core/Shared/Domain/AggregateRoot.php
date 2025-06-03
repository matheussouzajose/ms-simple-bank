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

namespace Core\Shared\Domain;

use Core\Shared\Domain\Event\DomainEvent;
use Core\Shared\Domain\ValueObject\Uuid;
use DateTime;
use SplObjectStorage;

abstract class AggregateRoot extends Entity
{
    /**
     * @var SplObjectStorage<DomainEvent, null>
     */
    protected SplObjectStorage $events;

    /**
     * @var SplObjectStorage<DomainEvent, null>
     */
    protected SplObjectStorage $dispatchedEvents;

    public function __construct(
        protected Uuid $id,
        protected DateTime $createdAt
    ) {
        parent::__construct(id: $id, createdAt: $createdAt);

        $this->initializeEvents();
    }

    public function applyEvent(DomainEvent $event): void
    {
        $this->events->attach($event);
    }

    public function markEventAsDispatched(DomainEvent $event): void
    {
        $this->dispatchedEvents->attach($event);
    }

    /**
     * @return DomainEvent[]
     */
    public function getUncommittedEvents(): array
    {
        $uncommitted = [];

        foreach ($this->events as $event) {
            if (! $this->dispatchedEvents->contains($event)) {
                $uncommitted[] = $event;
            }
        }

        return $uncommitted;
    }

    public function clearEvents(): void
    {
        $this->initializeEvents();
    }

    private function initializeEvents(): void
    {
        $this->events = new SplObjectStorage();
        $this->dispatchedEvents = new SplObjectStorage();
    }
}
