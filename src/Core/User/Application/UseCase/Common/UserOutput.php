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

namespace Core\User\Application\UseCase\Common;

use Core\User\Domain\Entity\User;

class UserOutput
{
    public function __construct(
        public string $id,
        public string $fullName,
        public string $email,
        public string $document,
        public string $type,
        public string $createdAt = '',
        public string $updatedAt = '',
    ) {
    }

    public static function toOutput(User $user): UserOutput
    {
        return new UserOutput(
            id: $user->id->value(),
            fullName: $user->fullName->value(),
            email: $user->email->value(),
            document: $user->document->value(),
            type: $user->type->value,
            createdAt: $user->createdAt->format('Y-m-d H:i:s'),
            updatedAt: $user->updatedAt->format('Y-m-d H:i:s'),
        );
    }
}
