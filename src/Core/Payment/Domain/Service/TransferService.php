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

namespace Core\Payment\Domain\Service;

use Core\Payment\Domain\Entity\Transaction;
use Core\Shared\Domain\Exception\ApplicationException;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\User\Domain\Entity\User;
use Core\Wallet\Domain\Entity\Wallet;
use Core\Wallet\Domain\ValueObject\Balance;
use DateTimeImmutable;

final class TransferService
{
    /**
     * @throws ValueObjectException
     * @throws ApplicationException
     */
    public function execute(User $payer, Wallet $payerWallet, Wallet $payeeWallet, Balance $amount): Transaction
    {
        if (! $payer->canTransfer()) {
            throw new ApplicationException(
                title: 'Transfer Not Allowed',
                status: 403,
                detail: 'The user is not permitted to initiate transfers.',
                extensions: [
                    'error_code' => 'TRANSFER_NOT_ALLOWED',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }

        if (! $payerWallet->hasSufficientBalance($amount)) {
            throw new ApplicationException(
                title: 'Insufficient Balance',
                status: 422,
                detail: 'The user does not have enough balance to perform this transfer.',
                extensions: [
                    'error_code' => 'INSUFFICIENT_FUNDS',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }

        $payerWallet->debit($amount);
        $payeeWallet->credit($amount);

        return Transaction::create(
            payerId: $payer->id,
            payeeId: $payeeWallet->userId,
            amount: $amount,
        );
    }
}
