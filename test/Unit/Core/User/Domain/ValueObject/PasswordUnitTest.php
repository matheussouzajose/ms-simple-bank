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
use Core\User\Domain\ValueObject\Pwd\PasswordFactory;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PasswordUnitTest extends TestCase
{
    public function testShouldCreatePlainTextPasswordAndValidate()
    {
        $password = PasswordFactory::build('textplain', 'Mudar@123', 'create');
        $this->assertEquals('Mudar@123', $password->value());

        $password = PasswordFactory::build('textplain', 'Mudar@123', 'restore');
        $this->assertEquals('Mudar@123', $password->value());

        $this->assertTrue($password->isValid('Mudar@123'));
    }

    public function testShouldCreateBcryptPasswordAndValidate()
    {
        $password = PasswordFactory::build('bcrypt', 'Mudar@123', 'create');
        $this->assertNotEquals('Mudar@123', $password->value());
        $this->assertTrue($password->isValid('Mudar@123'));
    }

    public function testShouldThrowExceptionWhenPasswordIsInvalid()
    {
        $this->expectException(ValueObjectException::class);
        $this->expectExceptionMessage('Invalid Password Format');

        PasswordFactory::build('bcrypt', '123', 'create');
    }
}
