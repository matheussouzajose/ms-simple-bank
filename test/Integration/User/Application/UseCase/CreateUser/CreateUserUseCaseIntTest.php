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

namespace HyperfTest\Integration\User\Application\UseCase\CreateUser;

use App\Model\User as UserModel;
use App\Model\Wallet as WalletModel;
use Core\Shared\Domain\Exception\ApplicationException;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Core\Shared\Infra\Persistence\Eloquent\UnitOfWorkEloquent;
use Core\User\Application\UseCase\CreateUser\CreateUserInput;
use Core\User\Application\UseCase\CreateUser\CreateUserUseCase;
use Core\User\Domain\Enum\UserType;
use Core\User\Infra\Persistence\Eloquent\UserEloquentRepository;
use Core\Wallet\Infra\Persistence\Eloquent\WalletEloquentRepository;
use HyperfTest\SetupTestCase;
use Throwable;

/**
 * @internal
 * @coversNothing
 */
class CreateUserUseCaseIntTest extends SetupTestCase
{
    protected UserEloquentRepository $userRepository;

    protected WalletEloquentRepository $walletRepository;

    protected UnitOfWorkInterface $unitOfWork;

    protected CreateUserUseCase $createUserUseCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitOfWork = new UnitOfWorkEloquent();
        $this->userRepository = new UserEloquentRepository(model: new UserModel(), unitOfWork: $this->unitOfWork);
        $this->walletRepository = new WalletEloquentRepository(model: new WalletModel(), unitOfWork: $this->unitOfWork);
        $this->createUserUseCase = new CreateUserUseCase(
            userRepository: $this->userRepository,
            walletRepository: $this->walletRepository,
            unitOfWork: $this->unitOfWork
        );
    }

    /**
     * @throws ValueObjectException
     * @throws ApplicationException|Throwable
     */
    public function testShouldThrowExceptionWhenEmailAlreadyExists()
    {
        $input = new CreateUserInput(
            fullName: 'John Doe',
            email: 'M4p5w@example.com',
            document: '63810244023',
            type: UserType::COMMON->value,
            password: 'securepassword',
            balance: 1000,
        );
        $this->createUserUseCase->execute($input);

        try {
            $this->createUserUseCase->execute($input);
        } catch (ApplicationException $e) {
            $this->assertEquals('User Already Exists', $e->title);
            $this->assertEquals(422, $e->status);
            $this->assertStringContainsString("A user with the email 'M4p5w@example.com' already exists.", $e->detail);
            $this->assertArrayHasKey('error_code', $e->extensions);
            $this->assertArrayHasKey('timestamp', $e->extensions);
        }
    }

    /**
     * @throws ValueObjectException
     * @throws ApplicationException|Throwable
     */
    public function testShouldThrowExceptionWhenDocumentAlreadyExists()
    {
        $input = new CreateUserInput(
            fullName: 'John Doe',
            email: 'M4p5w@example.com',
            document: '63810244023',
            type: UserType::COMMON->value,
            password: 'securepassword',
            balance: 1000,
        );
        $this->createUserUseCase->execute($input);

        try {
            $this->createUserUseCase->execute(new CreateUserInput(
                fullName: 'John Doe',
                email: 'M4p5w@example.com.br',
                document: '63810244023',
                type: UserType::COMMON->value,
                password: 'securepassword',
                balance: 1000,
            ));
        } catch (ApplicationException $e) {
            $this->assertEquals('User Already Exists', $e->title);
            $this->assertEquals(422, $e->status);
            $this->assertStringContainsString("A user with the document '63810244023' already exists.", $e->detail);
            $this->assertArrayHasKey('error_code', $e->extensions);
            $this->assertArrayHasKey('timestamp', $e->extensions);
        }
    }

    /**
     * @throws ValueObjectException
     * @throws ApplicationException
     * @throws Throwable
     */
    public function testShouldCreateUserWithCommonType()
    {
        $input = new CreateUserInput(
            fullName: 'John Doe',
            email: 'M4p5w@example.com',
            document: '63810244023',
            type: UserType::COMMON->value,
            password: 'securepassword',
            balance: 1000,
        );
        $output = $this->createUserUseCase->execute($input);

        $this->assertDatabaseHas('users', [
            'user_id' => $output->id,
            'full_name' => $input->fullName,
            'email' => $input->email,
            'document' => $input->document,
            'type' => UserType::COMMON->value,
        ]);

        $this->assertDatabaseHas('wallets', [
            'user_id' => $output->id,
            'balance' => $input->balance,
        ]);
    }
}
