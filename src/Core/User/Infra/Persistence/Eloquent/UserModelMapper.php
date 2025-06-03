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

namespace Core\User\Infra\Persistence\Eloquent;

use App\Model\User as UserModel;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\User\Domain\Entity\User;
use Core\User\Domain\Enum\UserType;
use Core\User\Domain\ValueObject\Document;
use Core\User\Domain\ValueObject\Email;
use Core\User\Domain\ValueObject\FullName;
use Core\User\Domain\ValueObject\Pwd\PasswordFactory;

class UserModelMapper
{
    /**
     * @throws ValueObjectException
     */
    public static function toEntity(UserModel $user): User
    {
        $entity = new User(
            id: new Uuid($user->user_id),
            createdAt: $user->created_at,
            updatedAt: $user->updated_at,
            deletedAt: $user->deleted_at,
            fullName: new FullName($user->full_name),
            email: new Email($user->email),
            document: new Document($user->document),
            type: UserType::from($user->type),
            password: PasswordFactory::restore('bcrypt', $user->password),
        );
        foreach ($user->wallets as $wallet) {
            $entity->addWalletId(new Uuid($wallet->wallet_id));
        }

        return $entity;
    }
}
