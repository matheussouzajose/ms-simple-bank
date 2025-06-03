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

namespace HyperfTest\Unit\Core\Wallet\Domain\ValueObject;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Wallet\Domain\ValueObject\Balance;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class BalanceUnitTest extends TestCase
{
    /**
     * @throws ValueObjectException
     */
    public function testShouldCreateBalanceAndPerformAddAndSubtract()
    {
        $balance = new Balance(12345);
        $this->assertEquals(12345, $balance->value());
        $this->assertEquals(123.45, $balance->toFloat());

        $bonus = Balance::fromFloat(50.75);

        $result = $balance->add($bonus);
        $this->assertEquals(17420, $result->value());

        $result = $balance->subtract($bonus);
        $this->assertEquals(7270, $result->value());
    }

    public function testShouldThrowExceptionWhenBalanceIsNegative()
    {
        $this->expectException(ValueObjectException::class);
        $this->expectExceptionMessage('Invalid Balance');

        new Balance(-1);
    }
}
