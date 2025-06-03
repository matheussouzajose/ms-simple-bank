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
use Respect\Validation\Validator as v;

final class Email extends ValueObject
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

    protected function isEqualTo(ValueObject $other): bool
    {
        return $other instanceof self && $this->value() === $other->value();
    }

    /**
     * @throws ValueObjectException
     */
    private function ensureIsValid(string $email): void
    {
        if (! v::email()->isValid($email)) {
            throw new ValueObjectException(
                title: 'Invalid Email Format',
                status: 422,
                detail: "The provided email \"{$email}\" is not valid. Please provide a valid email address.",
                extensions: [
                    'error_code' => 'INVALID_EMAIL',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
    }
}
