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

namespace Core\User\Domain\ValueObject\Pwd;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class Password extends ValueObject
{
    protected string $value;

    /**
     * @throws ValueObjectException
     */
    public function __construct(string $value, string $operation)
    {
        $this->ensureIsValid($value);

        $this->value = match ($operation) {
            'create' => $this->encrypt($value),
            'restore' => $value,
            default => throw new InvalidArgumentException("Invalid operation: {$operation}"),
        };
    }

    abstract public function type(): string;

    abstract public function isValid(string $plain): bool;

    public function value(): string
    {
        return $this->value;
    }

    abstract protected function encrypt(string $value): string;

    protected function isEqualTo(ValueObject $other): bool
    {
        return $other instanceof self && $this->value() === $other->value();
    }

    /**
     * @throws ValueObjectException
     */
    private function ensureIsValid(string $password): void
    {
        if (strlen($password) < 6) {
            throw new ValueObjectException(
                title: 'Invalid Password Format',
                status: 422,
                detail: "The provided password '{$password}' is not valid. It must contain at least 6 characters.",
                extensions: [
                    'error_code' => 'INVALID_PASSWORD',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
    }
}
