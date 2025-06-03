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

namespace Core\Payment\Domain\Event;

use Core\Shared\Domain\Event\IntegrationDomainEvent;

class TransactionCreatedIntegrationEvent extends IntegrationDomainEvent
{
    protected array $payload;

    public function __construct(TransactionCreatedEvent $event)
    {
        parent::__construct($event);
        $this->payload = [
            'id' => $event->aggregateId->value(),
        ];
    }
}
