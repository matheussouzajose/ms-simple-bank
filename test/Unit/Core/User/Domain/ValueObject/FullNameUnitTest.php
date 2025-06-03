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
use Core\User\Domain\ValueObject\FullName;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FullNameUnitTest extends TestCase
{
    public function testShouldCreateFullNameWithValidFirstAndLastName()
    {
        $fullName = new FullName('Jonh Doe');
        $this->assertEquals('Jonh Doe', $fullName->value());
        $this->assertEquals('Jonh', $fullName->firstName());
        $this->assertEquals('Doe', $fullName->lastName());
    }

    public function testShouldCompareFullNameEquality()
    {
        $fullName = new FullName('Jonh Doe');
        $fullNameEqual = new FullName('Jonh Doe');
        $this->assertTrue($fullName->equals($fullNameEqual));

        $fullName = new FullName('Jonh Doe');
        $fullNameEqual = new FullName('Jonh Doe 2');
        $this->assertFalse($fullName->equals($fullNameEqual));
    }

    public function testShouldThrowExceptionWhenFullNameIsInvalid()
    {
        $this->expectException(ValueObjectException::class);
        $this->expectExceptionMessage('Invalid Name Format');

        new FullName('Jonh');
    }
}
