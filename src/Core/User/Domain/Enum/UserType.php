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

namespace Core\User\Domain\Enum;

enum UserType: string
{
    case COMMON = 'COMMON';
    case MERCHANT = 'MERCHANT';

    public function canTransfer(): bool
    {
        return match ($this) {
            self::COMMON => true,
            self::MERCHANT => false,
        };
    }
}
