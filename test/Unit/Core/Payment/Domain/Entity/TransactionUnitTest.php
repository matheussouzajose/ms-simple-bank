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

namespace HyperfTest\Unit\Core\Payment\Domain\Entity;

use Core\Payment\Domain\Entity\Transaction;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Wallet\Domain\ValueObject\Balance;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class TransactionUnitTest extends TestCase
{
    /**
     * @throws ValueObjectException
     */
    public function testCreateValidTransaction()
    {
        $payerId = new Uuid();
        $payeeId = new Uuid();
        $transaction = Transaction::create(
            payerId: $payerId,
            payeeId: $payeeId,
            amount: new Balance(10000)
        );
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($payerId, $transaction->payerId);
        $this->assertEquals($payeeId, $transaction->payeeId);
        $this->assertEquals(10000, $transaction->amount->value());
    }
}
