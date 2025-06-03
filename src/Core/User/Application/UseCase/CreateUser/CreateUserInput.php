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

namespace Core\User\Application\UseCase\CreateUser;

readonly class CreateUserInput
{
    public function __construct(
        readonly string $fullName,
        readonly string $email,
        readonly string $document,
        readonly string $type,
        readonly string $password,
        readonly ?int $balance = null,
    ) {
    }
}
