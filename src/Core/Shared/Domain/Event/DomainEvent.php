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

use Core\Shared\Domain\ValueObject;
use Core\Shared\Traits\MethodsMagicsTrait;
use DateTime;

abstract class DomainEvent
{
    use MethodsMagicsTrait;

    public function __construct(readonly ValueObject $aggregateId, readonly DateTime $occurredOn)
    {
    }

    abstract public function integrationEvent(): ?IntegrationDomainEvent;
}
