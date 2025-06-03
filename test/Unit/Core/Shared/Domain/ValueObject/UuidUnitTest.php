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

namespace HyperfTest\Unit\Core\Shared\Domain\ValueObject;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject\Uuid;
use Hyperf\Testing\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

/**
 * @internal
 * @coversNothing
 */
class UuidUnitTest extends TestCase
{
    public function testShouldCreateValidUuid(): void
    {
        $uuid = new Uuid();
        $this->assertNotEmpty($uuid->value());
        $this->assertTrue(RamseyUuid::isValid($uuid->value()));
    }

    /**
     * @throws ValueObjectException
     */
    public function testShouldCreateUuidWithGivenValue(): void
    {
        $value = '550e8400-e29b-41d4-a716-446655440000';
        $uuid = new Uuid($value);
        $this->assertSame($value, $uuid->value());
    }

    public function testShouldThrowExceptionWhenUuidIsInvalid(): void
    {
        $this->expectException(ValueObjectException::class);
        $this->expectExceptionMessage('Invalid UUID Format');

        new Uuid('invalid-uuid');
    }

    public function testToStringMethodMatchesValue(): void
    {
        $uuid = new Uuid();
        $this->assertSame($uuid->value(), (string) $uuid);
    }

    /**
     * @throws ValueObjectException
     */
    public function testEqualityBetweenSameValueUuids(): void
    {
        $uuidStr = RamseyUuid::uuid4()->toString();
        $uuidA = new Uuid($uuidStr);
        $uuidB = new Uuid($uuidStr);

        $this->assertTrue($uuidA->equals($uuidB));
    }

    public function testInequalityBetweenDifferentUuids(): void
    {
        $uuidA = new Uuid();
        $uuidB = new Uuid();

        $this->assertFalse($uuidA->equals($uuidB));
    }
}
