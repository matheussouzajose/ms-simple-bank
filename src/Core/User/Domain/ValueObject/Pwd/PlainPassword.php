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

namespace Core\User\Domain\ValueObject\Pwd;

final class PlainPassword extends Password
{
    public function type(): string
    {
        return 'textplain';
    }

    public function isValid(string $plain): bool
    {
        return $this->value === $plain;
    }

    protected function encrypt(string $value): string
    {
        return $value;
    }
}
