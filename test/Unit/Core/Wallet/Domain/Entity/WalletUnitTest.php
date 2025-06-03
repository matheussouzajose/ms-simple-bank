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

namespace HyperfTest\Unit\Core\Wallet\Domain\Entity;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Wallet\Domain\Entity\Wallet;
use Core\Wallet\Domain\ValueObject\Balance;
use DateTime;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class WalletUnitTest extends TestCase
{
    /**
     * @throws ValueObjectException
     */
    public function testShouldCreateWalletWithValidUserAndBalance()
    {
        $userId = new Uuid();
        $wallet = Wallet::create(
            userId: $userId,
            balance: new Balance(100),
        );
        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals($userId, $wallet->userId->value());
        $this->assertEquals(100, $wallet->balance->value());
        $this->assertInstanceOf(DateTime::class, $wallet->createdAt);
        $this->assertInstanceOf(DateTime::class, $wallet->updatedAt);
        $this->assertNull($wallet->deletedAt);
    }

    /**
     * @throws ValueObjectException
     */
    public function testShouldDebitWalletBalance()
    {
        $userId = new Uuid();
        $wallet = Wallet::create(
            userId: $userId,
            balance: new Balance(100),
        );
        $wallet->debit(new Balance(50));
        $this->assertEquals(50, $wallet->balance->value());
    }

    /**
     * @throws ValueObjectException
     */
    public function testShouldCreditWalletBalance()
    {
        $userId = new Uuid();
        $wallet = Wallet::create(
            userId: $userId,
            balance: new Balance(100),
        );
        $wallet->credit(new Balance(50));
        $this->assertEquals(150, $wallet->balance->value());
    }

    /**
     * @throws ValueObjectException
     */
    public function testShouldCheckIfWalletHasSufficientBalance()
    {
        $userId = new Uuid();
        $wallet = Wallet::create(
            userId: $userId,
            balance: new Balance(100),
        );
        $this->assertTrue($wallet->hasSufficientBalance(new Balance(50)));
        $this->assertFalse($wallet->hasSufficientBalance(new Balance(150)));
    }
}
