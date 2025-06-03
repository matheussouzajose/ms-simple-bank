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

namespace HyperfTest\Unit\Core\User\Domain\ValueObject;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\User\Domain\ValueObject\Email;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class EmailUnitTest extends TestCase
{
    public function testShouldCreateEmailWithValidAddress()
    {
        $email = new Email('john.doe@example.com');
        $this->assertEquals('john.doe@example.com', $email->value());
    }

    public function testShouldThrowExceptionWhenEmailIsInvalid()
    {
        $this->expectException(ValueObjectException::class);
        $this->expectExceptionMessage('Invalid Email Format');

        new Email('Jonh');
    }
}
