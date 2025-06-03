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

namespace HyperfTest\Integration\Core\Payment\Transaction\Application\UseCase\Transfer;

use App\Model\Transaction as TransactionModel;
use App\Model\User as UserModel;
use App\Model\Wallet as WalletModel;
use Core\Payment\Application\UseCase\Common\TransferOutput;
use Core\Payment\Application\UseCase\Transfer\TransferInput;
use Core\Payment\Application\UseCase\Transfer\TransferUseCase;
use Core\Payment\Domain\Service\TransferService;
use Core\Payment\Infra\Gateway\AuthorizationGatewayInterface;
use Core\Payment\Infra\Persistence\Eloquent\TransactionEloquentRepository;
use Core\Shared\Application\ApplicationService;
use Core\Shared\Domain\Event\DomainEventMediator;
use Core\Shared\Domain\Exception\ApplicationException;
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
use Core\Wallet\Infra\Persistence\Eloquent\WalletEloquentRepository;
use Hyperf\Event\EventDispatcher;
use HyperfTest\SetupTestCase;
use Throwable;

/**
 * @internal
 * @coversNothing
 */
class TransferUseCaseIntTest extends SetupTestCase
{
    protected UserEloquentRepository $userRepository;

    protected WalletEloquentRepository $walletRepository;

    protected TransactionEloquentRepository $transactionRepository;

    protected TransferService $transferService;

    protected UnitOfWorkInterface $unitOfWork;

    protected AuthorizationGatewayInterface $authorizationGateway;

    protected ApplicationService $applicationService;

    protected TransferUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitOfWork = new UnitOfWorkEloquent();
        $this->userRepository = new UserEloquentRepository(new UserModel(), $this->unitOfWork);
        $this->walletRepository = new WalletEloquentRepository(new WalletModel(), $this->unitOfWork);
        $this->transactionRepository = new TransactionEloquentRepository(new TransactionModel(), $this->unitOfWork);
        $this->transferService = new TransferService();
        $this->authorizationGateway = $this->createMock(AuthorizationGatewayInterface::class);
        $this->applicationService = new ApplicationService(
            uow: $this->unitOfWork,
            domainEventMediator: new DomainEventMediator(
                dispatcher: $this->container->get(EventDispatcher::class),
            ),
        );
        $this->useCase = new TransferUseCase(
            userRepository: $this->userRepository,
            walletRepository: $this->walletRepository,
            transactionRepository: $this->transactionRepository,
            applicationService: $this->applicationService,
            transferService: $this->transferService,
            authorizationGateway: $this->authorizationGateway,
        );
    }

    /**
     * @throws ValueObjectException
     * @throws Throwable
     */
    public function testSelfTransferShouldFail(): void
    {
        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('Self Transfer Not Allowed');

        $user = $this->createUser(type: UserType::COMMON, document: '42600503030');
        $this->createWallet($user, 10000);

        $input = new TransferInput(
            payerId: $user->id->value(),
            payeeId: $user->id->value(),
            amount: 1000,
        );

        $this->useCase->execute($input);
    }

    /**
     * @throws ValueObjectException
     * @throws Throwable
     */
    public function testMissingParticipantShouldFail(): void
    {
        $this->authorizationGateway->method('authorize')->willReturn(true);

        $payer = $this->createUser(UserType::COMMON, document: '42600503030');
        $this->createWallet($payer, 10000);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('Invalid Transfer Participants');

        $input = new TransferInput(
            payerId: $payer->id->value(),
            payeeId: '00000000-0000-0000-0000-000000000000',
            amount: 1000,
        );

        $this->useCase->execute($input);
    }

    /**
     * @throws ValueObjectException
     * @throws Throwable
     */
    public function testUnauthorizedTransferShouldFail(): void
    {
        $this->authorizationGateway->method('authorize')->willReturn(false);

        $payer = $this->createUser(UserType::COMMON, document: '42600503030');
        $payee = $this->createUser(UserType::MERCHANT, document: '63685074000129');
        $this->createWallet($payer, 10000);
        $this->createWallet($payee, 10000);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('Unauthorized Transfer');

        $input = new TransferInput(
            payerId: $payer->id->value(),
            payeeId: $payee->id->value(),
            amount: 1000,
        );

        $this->useCase->execute($input);
    }

    /**
     * @throws ValueObjectException
     * @throws Throwable
     */
    public function testShouldTransferSuccessfully(): void
    {
        $this->authorizationGateway->method('authorize')->willReturn(true);

        $payer = $this->createUser(UserType::COMMON, document: '42600503030');
        $payee = $this->createUser(UserType::MERCHANT, document: '63685074000129');
        $this->createWallet($payer, 10000);
        $this->createWallet($payee, 10000);

        $input = new TransferInput(
            payerId: $payer->id->value(),
            payeeId: $payee->id->value(),
            amount: 1000,
        );

        $output = $this->useCase->execute($input);
        $this->assertInstanceOf(TransferOutput::class, $output);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $payer->id->value(),
            'balance' => 9000,
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $payee->id->value(),
            'balance' => 11000,
        ]);
        $this->assertDatabaseHas('transactions', [
            'payer_id' => $payer->id->value(),
            'payee_id' => $payee->id->value(),
            'amount' => 1000,
        ]);
    }

    /**
     * @throws ValueObjectException
     */
    private function createUser(UserType $type, string $document): User
    {
        $user = User::create(
            fullName: new FullName('User Test '),
            email: new Email("user{$document}@example.com"),
            document: new Document($document),
            type: $type,
            password: PasswordFactory::create('bcrypt', 'secure'),
        );
        UserModel::create($user->toArray());
        return $user;
    }

    private function createWallet(User $user, int $balance): void
    {
        WalletModel::create([
            'wallet_id' => $user->id->value(),
            'user_id' => $user->id->value(),
            'balance' => $balance,
        ]);
    }
}
