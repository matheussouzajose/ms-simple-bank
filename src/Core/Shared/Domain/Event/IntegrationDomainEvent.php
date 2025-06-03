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

use Core\Shared\Traits\MethodsMagicsTrait;
use DateTime;

abstract class IntegrationDomainEvent
{
    use MethodsMagicsTrait;

    protected DateTime $occurredOn;

    protected string $eventName;

    public function __construct(DomainEvent $event)
    {
        $this->occurredOn = $event->occurredOn;
        $this->eventName = get_class($event);
    }
}
