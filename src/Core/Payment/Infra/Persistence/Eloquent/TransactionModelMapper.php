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

namespace Core\Payment\Infra\Persistence\Eloquent;

use App\Model\Transaction as TransactionModel;
use Core\Payment\Domain\Entity\Transaction;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Wallet\Domain\ValueObject\Balance;

class TransactionModelMapper
{
    /**
     * @throws ValueObjectException
     */
    public static function toEntity(TransactionModel $transaction): Transaction
    {
        return new Transaction(
            id: new Uuid($transaction->transaction_id),
            createdAt: $transaction->created_at,
            payerId: new Uuid($transaction->payer_id),
            payeeId: new Uuid($transaction->payee_id),
            amount: new Balance($transaction->amount),
        );
    }
}
