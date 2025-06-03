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

namespace Core\Payment\Application\UseCase\Transfer;

use Core\Payment\Application\UseCase\Common\TransferOutput;
use Core\Payment\Domain\Entity\Transaction;
use Core\Payment\Domain\Repository\TransactionRepositoryInterface;
use Core\Payment\Domain\Service\TransferService;
use Core\Payment\Infra\Gateway\AuthorizationGatewayInterface;
use Core\Shared\Application\ApplicationService;
use Core\Shared\Domain\Exception\ApplicationException;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\User\Domain\Repository\UserRepositoryInterface;
use Core\Wallet\Domain\Repository\WalletRepositoryInterface;
use Core\Wallet\Domain\ValueObject\Balance;
use Core\Wallet\Infra\Criteria\ByUserIdCriteria;
use DateTimeImmutable;
use Throwable;

readonly class TransferUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository,
        private TransactionRepositoryInterface $transactionRepository,
        private ApplicationService $applicationService,
        private TransferService $transferService,
        private AuthorizationGatewayInterface $authorizationGateway
    ) {
    }

    /**
     * @throws ApplicationException
     * @throws Throwable
     */
    public function execute(TransferInput $input): TransferOutput
    {
        $payerId = new Uuid($input->payerId);
        $payeeId = new Uuid($input->payeeId);

        if ($payerId->equals($payeeId)) {
            throw $this->selfTransferNotAllowedException();
        }

        [$payer, $payee] = [
            $this->userRepository->findById($payerId),
            $this->userRepository->findById($payeeId),
        ];

        [$payerWallet, $payeeWallet] = [
            $this->walletRepository->findByCriteria(new ByUserIdCriteria($payerId)),
            $this->walletRepository->findByCriteria(new ByUserIdCriteria($payeeId)),
        ];

        if (! $payer || ! $payee || ! $payerWallet || ! $payeeWallet) {
            throw $this->invalidParticipantsException();
        }

        if (! $this->authorizationGateway->authorize()) {
            throw $this->unauthorizedTransferException();
        }

        $transaction = $this->transferService->execute(
            payer: $payer,
            payerWallet: $payerWallet,
            payeeWallet: $payeeWallet,
            amount: new Balance($input->amount),
        );

        $this->applicationService->run(fn () => $this->persistEntities($payerWallet, $payeeWallet, $transaction));

        return TransferOutput::toOutput(transaction: $transaction, payer: $payer, payee: $payee);
    }

    private function persistEntities($payerWallet, $payeeWallet, Transaction $transaction): void
    {
        $this->walletRepository->update($payerWallet);
        $this->walletRepository->update($payeeWallet);
        $this->transactionRepository->insert($transaction);
    }

    private function invalidParticipantsException(): ApplicationException
    {
        return new ApplicationException(
            title: 'Invalid Transfer Participants',
            status: 422,
            detail: 'The payer or payee information provided is invalid or incomplete.',
            extensions: [
                'error_code' => 'INVALID_PARTICIPANTS',
                'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
            ]
        );
    }

    private function unauthorizedTransferException(): ApplicationException
    {
        return new ApplicationException(
            title: 'Unauthorized Transfer',
            status: 403,
            detail: 'The external authorization service denied this transaction.',
            extensions: [
                'error_code' => 'UNAUTHORIZED_TRANSFER',
                'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
            ]
        );
    }

    private function selfTransferNotAllowedException(): ApplicationException
    {
        return new ApplicationException(
            title: 'Self Transfer Not Allowed',
            status: 422,
            detail: 'A user cannot transfer money to themselves.',
            extensions: [
                'error_code' => 'SELF_TRANSFER_NOT_ALLOWED',
                'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
            ]
        );
    }
}
