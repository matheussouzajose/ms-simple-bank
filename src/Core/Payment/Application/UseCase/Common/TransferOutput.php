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

namespace Core\Payment\Application\UseCase\Common;

use Core\Payment\Domain\Entity\Transaction;
use Core\User\Application\UseCase\Common\UserOutput;
use Core\User\Domain\Entity\User;

readonly class TransferOutput
{
    public function __construct(
        public string $id,
        public UserOutput $payer,
        public UserOutput $payee,
        public int $amount,
        public string $createdAt = '',
    ) {
    }

    public static function toOutput(Transaction $transaction, User $payer, User $payee): TransferOutput
    {
        return new TransferOutput(
            id: $transaction->id->value(),
            payer: UserOutput::toOutput($payer),
            payee: UserOutput::toOutput($payee),
            amount: $transaction->amount->value(),
            createdAt: $transaction->createdAt->format('Y-m-d H:i:s'),
        );
    }
}
