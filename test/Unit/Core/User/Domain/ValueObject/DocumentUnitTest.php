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
use Core\User\Domain\ValueObject\Document;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class DocumentUnitTest extends TestCase
{
    public function testShouldCreateDocumentWithValidCpfAndCnpj()
    {
        $cpf = new Document('638.102.440-23');
        $this->assertEquals('63810244023', $cpf->value());

        $cnpj = new Document('00.093.780/0001-61');
        $this->assertEquals('00093780000161', $cnpj->value());
    }

    public function testShouldThrowExceptionWhenDocumentIsInvalid()
    {
        $this->expectException(ValueObjectException::class);
        $this->expectExceptionMessage('Invalid Document Format');

        new Document('Jonh');
    }
}
