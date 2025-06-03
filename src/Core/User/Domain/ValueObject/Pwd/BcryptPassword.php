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

final class BcryptPassword extends Password
{
    public function type(): string
    {
        return 'bcrypt';
    }

    public function isValid(string $plain): bool
    {
        return password_verify($plain, $this->value);
    }

    protected function encrypt(string $value): string
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }
}
