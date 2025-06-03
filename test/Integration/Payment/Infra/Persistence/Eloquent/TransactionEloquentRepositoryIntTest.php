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

namespace HyperfTest\Integration\Core\Payemnt\Transaction\Infra\Persistence\Eloquent;

use App\Model\Transaction as TransactionModel;
use Core\Payment\Domain\Entity\Transaction;
use Core\Payment\Infra\Persistence\Eloquent\TransactionEloquentRepository;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Core\Shared\Infra\Persistence\Eloquent\UnitOfWorkEloquent;
use Core\User\Domain\Entity\User;
use Core\User\Domain\Enum\UserType;
use Core\User\Domain\ValueObject\Document;
use Core\User\Domain\ValueObject\Email;
use Core\User\Domain\ValueObject\FullName;
use Core\User\Domain\ValueObject\Pwd\PasswordFactory;
use Core\User\Infra\Persistence\Eloquent\UserEloquentRepository;
use Core\Wallet\Domain\Entity\Wallet;
use Core\Wallet\Domain\ValueObject\Balance;
use Core\Wallet\Infra\Persistence\Eloquent\WalletEloquentRepository;
use HyperfTest\SetupTestCase;

/**
 * @internal
 * @coversNothing
 */
class TransactionEloquentRepositoryIntTest extends SetupTestCase
{
    protected TransactionEloquentRepository $repository;

    protected UnitOfWorkInterface $unitOfWork;

    protected WalletEloquentRepository $walletRepository;

    protected UserEloquentRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitOfWork = new UnitOfWorkEloquent();
        $this->walletRepository = new WalletEloquentRepository(model: new \App\Model\Wallet(), unitOfWork: $this->unitOfWork);
        $this->userRepository = new UserEloquentRepository(model: new \App\Model\User(), unitOfWork: $this->unitOfWork);
        $this->repository = new TransactionEloquentRepository(model: new TransactionModel(), unitOfWork: $this->unitOfWork);
    }

    /**
     * @throws ValueObjectException
     */
    public function testInsertTransaction()
    {
        $payer = User::create(
            fullName: new FullName('John Doe'),
            email: new Email('john.doe@example.com'),
            document: new Document('638.102.440-23'),
            type: UserType::COMMON,
            password: PasswordFactory::create('bcrypt', 'securepassword'),
        );
        $this->userRepository->insert($payer);
        $payerWallet = Wallet::create(
            userId: $payer->id,
            balance: new Balance(10000),
        );
        $this->walletRepository->insert($payerWallet);

        $payee = User::create(
            fullName: new FullName('John Doe'),
            email: new Email('john.doe2@example.com'),
            document: new Document('554.386.520-15'),
            type: UserType::MERCHANT,
            password: PasswordFactory::create('bcrypt', 'securepassword'),
        );
        $this->userRepository->insert($payee);
        $payeeWallet = Wallet::create(
            userId: $payee->id,
            balance: new Balance(10000),
        );
        $this->walletRepository->insert($payeeWallet);

        $transaction = Transaction::create(
            payerId: $payer->id,
            payeeId: $payee->id,
            amount: new Balance(100),
        );

        $this->repository->insert($transaction);
        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $transaction->id->value(),
            'created_at' => $transaction->createdAt->format('Y-m-d H:i:s'),
            'payer_id' => $transaction->payerId->value(),
            'payee_id' => $transaction->payeeId->value(),
            'amount' => $transaction->amount->value(),
        ]);
    }
}
