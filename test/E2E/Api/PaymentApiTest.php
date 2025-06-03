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

namespace HyperfTest\E2E\Api;

use App\Model\User as UserModel;
use App\Model\Wallet as WalletModel;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\User\Domain\Entity\User;
use Core\User\Domain\Enum\UserType;
use Core\User\Domain\ValueObject\Document;
use Core\User\Domain\ValueObject\Email;
use Core\User\Domain\ValueObject\FullName;
use Core\User\Domain\ValueObject\Pwd\PasswordFactory;
use HyperfTest\SetupTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @coversNothing
 */
class PaymentApiTest extends SetupTestCase
{
    protected string $endpoint = '/api/v1/transfer';

    public function testValidationsStore()
    {
        $data = [];

        $response = $this->post($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'type',
            'title',
            'status',
            'detail',
            'extensions' => [
                'errors',
                'timestamp',
            ],
        ]);
    }

    public function testShouldBeThrowErrorWhenInvalidGivenInvalidUser()
    {
        $data = [
            'value' => 10000,
            'payerId' => 'invalid-user-id',
            'payeeId' => 'invalid-user-id',
        ];

        $response = $this->post($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'type',
            'title',
            'status',
            'detail',
            'extensions' => [
                'errors',
                'timestamp',
            ],
        ]);
    }

    public function testShouldBeCreateTransactionSuccessfully()
    {
        $payer = $this->createUser(UserType::COMMON, document: '42600503030');
        $payee = $this->createUser(UserType::MERCHANT, document: '63685074000129');
        $this->createWallet($payer, 10000);
        $this->createWallet($payee, 10000);

        $data = [
            'value' => 1000,
            'payerId' => $payer->id->value(),
            'payeeId' => $payee->id->value(),
        ];

        $this->post($this->endpoint, $data);
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
