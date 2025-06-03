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

use Core\Shared\Domain\ValueObject\Uuid;
use Core\Shared\Traits\MethodsMagicsTrait;
use DateTime;

abstract class Entity
{
    use MethodsMagicsTrait;

    protected Notification $notification;

    public function __construct(
        protected Uuid $id,
        protected DateTime $createdAt
    ) {
        $this->notification = new Notification();
    }

    abstract public function toArray(): array;
}
