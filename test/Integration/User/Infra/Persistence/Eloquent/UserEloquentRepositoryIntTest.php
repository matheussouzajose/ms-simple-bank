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

namespace HyperfTest\Integration\User\Infra\Persistence\Eloquent;

use App\Model\User as UserModel;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Core\Shared\Infra\Persistence\Eloquent\UnitOfWorkEloquent;
use Core\User\Domain\Entity\User;
use Core\User\Domain\Enum\UserType;
use Core\User\Domain\ValueObject\Document;
use Core\User\Domain\ValueObject\Email;
use Core\User\Domain\ValueObject\FullName;
use Core\User\Domain\ValueObject\Pwd\PasswordFactory;
use Core\User\Infra\Persistence\Eloquent\UserEloquentRepository;
use HyperfTest\SetupTestCase;

/**
 * @internal
 * @coversNothing
 */
class UserEloquentRepositoryIntTest extends SetupTestCase
{
    protected UserEloquentRepository $repository;

    protected UnitOfWorkInterface $unitOfWork;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitOfWork = new UnitOfWorkEloquent();
        $this->repository = new UserEloquentRepository(model: new UserModel(), unitOfWork: $this->unitOfWork);
    }

    public function testShouldInsertUserIntoDatabase()
    {
        $user = User::create(
            fullName: new FullName('John Doe'),
            email: new Email('john.doe@example.com'),
            document: new Document('638.102.440-23'),
            type: UserType::COMMON,
            password: PasswordFactory::create('bcrypt', 'securepassword'),
        );

        $this->repository->insert($user);
        $this->assertDatabaseHas('users', [
            'user_id' => $user->id->value(),
            'created_at' => $user->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $user->updatedAt->format('Y-m-d H:i:s'),
            'deleted_at' => $user->deletedAt,
            'full_name' => $user->fullName->value(),
            'email' => $user->email->value(),
            'password' => $user->password->value(),
            'document' => $user->document->value(),
            'type' => $user->type->value,
        ]);
    }
}
