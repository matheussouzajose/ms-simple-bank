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

namespace HyperfTest\Unit\Core\User\Domain\Entity;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\User\Domain\Entity\User;
use Core\User\Domain\Enum\UserType;
use Core\User\Domain\ValueObject\Document;
use Core\User\Domain\ValueObject\Email;
use Core\User\Domain\ValueObject\FullName;
use Core\User\Domain\ValueObject\Pwd\PasswordFactory;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class UserUnitTest extends TestCase
{
    /**
     * @throws ValueObjectException
     */
    public function testShouldCreateUserWithValidData(): void
    {
        $user = $this->createUser();

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->toArray()['full_name']);
        $this->assertEquals('john.doe@example.com', $user->toArray()['email']);
        $this->assertEquals('63810244023', $user->toArray()['document']);
        $this->assertEquals(UserType::COMMON->value, $user->toArray()['type']);
        $this->assertNotEmpty($user->toArray()['user_id']);
        $this->assertNull($user->toArray()['deleted_at']);
    }

    /**
     * @throws ValueObjectException
     */
    public function testShouldCheckIfUserCanTransferBasedOnType(): void
    {
        $common = $this->createUser(UserType::COMMON);
        $this->assertTrue($common->canTransfer());

        $merchant = $this->createUser(UserType::MERCHANT);
        $this->assertFalse($merchant->canTransfer());
    }

    /**
     * @throws ValueObjectException
     */
    public function testToArrayReturnsAllFields(): void
    {
        $user = $this->createUser();
        $data = $user->toArray();

        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertArrayHasKey('deleted_at', $data);
        $this->assertArrayHasKey('full_name', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('document', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('password', $data);
    }

    /**
     * @throws ValueObjectException
     */
    private function createUser(UserType $type = UserType::COMMON): User
    {
        return User::create(
            fullName: new FullName('John Doe'),
            email: new Email('john.doe@example.com'),
            document: new Document('638.102.440-23'),
            type: $type,
            password: PasswordFactory::create('bcrypt', 'securepassword'),
        );
    }
}
