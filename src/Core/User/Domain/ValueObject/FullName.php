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

namespace Core\User\Domain\ValueObject;

use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject;
use DateTimeImmutable;

final class FullName extends ValueObject
{
    /**
     * @throws ValueObjectException
     */
    public function __construct(private readonly string $value)
    {
        $this->ensureIsValid($this->value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function firstName(): string
    {
        return explode(' ', $this->value)[0];
    }

    public function lastName(): string
    {
        return array_slice(explode(' ', $this->value), -1)[0];
    }

    protected function isEqualTo(ValueObject $other): bool
    {
        return $other instanceof self && $this->value() === $other->value();
    }

    /**
     * @throws ValueObjectException
     */
    private function ensureIsValid(string $name): void
    {
        if (str_word_count($name) < 2) {
            throw new ValueObjectException(
                title: 'Invalid Name Format',
                status: 422,
                detail: "The provided name \"{$name}\" is not valid. Please provide a complete name (e.g., 'John Doe').",
                extensions: [
                    'error_code' => 'INVALID_NAME',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }

        if (mb_strlen($name) > 255) {
            throw new ValueObjectException(
                title: 'Name Too Long',
                status: 422,
                detail: 'The provided name exceeds the maximum allowed length of 255 characters.',
                extensions: [
                    'error_code' => 'NAME_TOO_LONG',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
    }
}
