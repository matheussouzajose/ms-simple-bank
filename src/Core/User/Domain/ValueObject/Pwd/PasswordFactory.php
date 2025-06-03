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

use Core\Shared\Domain\Exception\ValueObjectException;
use InvalidArgumentException;

final class PasswordFactory
{
    /**
     * @throws ValueObjectException
     */
    public static function build(string $type, string $password, string $operation): Password
    {
        return match ($type) {
            'textplain' => new PlainPassword($password, $operation),
            'bcrypt' => new BcryptPassword($password, $operation),
            default => throw new InvalidArgumentException("Invalid password type: {$type}"),
        };
    }

    /**
     * @throws ValueObjectException
     */
    public static function create(string $type, string $password): Password
    {
        return self::build($type, $password, 'create');
    }

    /**
     * @throws ValueObjectException
     */
    public static function restore(string $type, string $password): Password
    {
        return self::build($type, $password, 'restore');
    }
}
