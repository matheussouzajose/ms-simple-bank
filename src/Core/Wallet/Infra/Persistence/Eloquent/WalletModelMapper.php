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

namespace Core\Wallet\Infra\Persistence\Eloquent;

use App\Model\Wallet as WalletModel;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Wallet\Domain\Entity\Wallet;
use Core\Wallet\Domain\ValueObject\Balance;

class WalletModelMapper
{
    /**
     * @throws ValueObjectException
     */
    public static function toEntity(WalletModel $wallet): Wallet
    {
        return new Wallet(
            id: new Uuid($wallet->wallet_id),
            createdAt: $wallet->created_at,
            updatedAt: $wallet->updated_at,
            deletedAt: $wallet->deleted_at,
            userId: new Uuid($wallet->user_id),
            balance: new Balance($wallet->balance),
        );
    }
}
