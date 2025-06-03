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

use Core\Shared\Domain\Event\DomainEvent;
use Core\Shared\Domain\Event\IntegrationDomainEvent;
use Core\Shared\Domain\ValueObject\Uuid;
use DateTime;

class TransactionCreatedEvent extends DomainEvent
{
    public function __construct(Uuid $aggregateId)
    {
        parent::__construct(aggregateId: $aggregateId, occurredOn: new DateTime());
    }

    public function integrationEvent(): ?IntegrationDomainEvent
    {
        return new TransactionCreatedIntegrationEvent($this);
    }
}
