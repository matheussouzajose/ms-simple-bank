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

namespace Core\Shared\Domain\ValueObject;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid as RamseyUuid;

final class Uuid extends ValueObject
{
    private string $value;

    /**
     * @throws ValueObjectException
     */
    public function __construct(?string $value = null)
    {
        $this->value = $value ?? RamseyUuid::uuid4()->toString();
        $this->ensureIsValid($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    protected function isEqualTo(ValueObject $other): bool
    {
        return $other instanceof self && $this->value() === $other->value();
    }

    /**
     * @throws ValueObjectException
     */
    private function ensureIsValid(string $id): void
    {
        if (! RamseyUuid::isValid($id)) {
            throw new ValueObjectException(
                title: 'Invalid UUID Format',
                status: 422,
                detail: "The provided UUID {$id} is not valid.",
                extensions: [
                    'error_code' => 'INVALID_UUID',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
    }
}
