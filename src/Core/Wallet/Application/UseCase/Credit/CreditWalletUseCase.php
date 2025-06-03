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

namespace Core\Wallet\Application\UseCase\Credit;

use Core\Shared\Domain\Exception\ApplicationException;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\User\Domain\Repository\UserRepositoryInterface;
use Core\Wallet\Domain\Repository\WalletRepositoryInterface;
use Core\Wallet\Domain\ValueObject\Balance;
use DateTimeImmutable;

readonly class CreditWalletUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository,
        private UnitOfWorkInterface $unitOfWork,
    ) {
    }

    /**
     * @throws ValueObjectException
     * @throws ApplicationException
     */
    public function execute(CreditWalletInput $input): void
    {
        $userId = new Uuid($input->userId);
        if (! $user = $this->userRepository->findById($userId)) {
            throw new ApplicationException(
                title: 'User Not Found',
                status: 422,
                detail: "User with ID \"{$userId->value()}\" not found.",
                extensions: [
                    'error_code' => 'USER_NOT_FOUND',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }

        $walletId = new Uuid($input->walletId);
        if (! $user->hasWallet($walletId)) {
            throw new ApplicationException(
                title: 'Wallet Id Not Found',
                status: 422,
                detail: "Wallet with ID \"{$walletId->value()}\" not found for user with ID \"{$userId->value()}\".",
                extensions: [
                    'error_code' => 'WALLET_ALREADY_EXISTS',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }

        $walletId = new Uuid($input->walletId);
        $wallet = $this->walletRepository->findById($walletId);
        $wallet->credit(new Balance($input->balance));

        $this->unitOfWork->do(function () use ($wallet) {
            $this->walletRepository->update($wallet);
        });
    }
}
