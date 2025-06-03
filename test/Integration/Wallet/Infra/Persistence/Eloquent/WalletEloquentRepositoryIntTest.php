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

namespace HyperfTest\Integration\Core\Wallet\Infra\Persistence\Eloquent;

use App\Model\User as UserModel;
use App\Model\Wallet as WalletModel;
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
class WalletEloquentRepositoryIntTest extends SetupTestCase
{
    protected WalletEloquentRepository $repository;

    protected UnitOfWorkInterface $unitOfWork;

    protected UserEloquentRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitOfWork = new UnitOfWorkEloquent();
        $this->repository = new WalletEloquentRepository(model: new WalletModel(), unitOfWork: $this->unitOfWork);
        $this->userRepository = new UserEloquentRepository(model: new UserModel(), unitOfWork: $this->unitOfWork);
    }

    /**
     * @throws ValueObjectException
     */
    public function testShouldCreateWallet()
    {
        $user = User::create(
            fullName: new FullName('John Doe'),
            email: new Email('john.doe@example.com'),
            document: new Document('638.102.440-23'),
            type: UserType::COMMON,
            password: PasswordFactory::create('bcrypt', 'securepassword'),
        );
        $this->userRepository->insert($user);

        $wallet = Wallet::create(
            userId: $user->id,
            balance: new Balance(100),
        );

        $this->repository->insert($wallet);
        $this->assertDatabaseHas('wallets', [
            'wallet_id' => $wallet->id->value(),
        ]);
    }
}
