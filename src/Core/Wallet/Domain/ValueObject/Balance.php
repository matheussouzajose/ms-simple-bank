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

namespace Core\Wallet\Domain\ValueObject;

use Core\Shared\Domain\Exception\ValueObjectException;
use DateTimeImmutable;
use DateTimeInterface;

final readonly class Balance
{
    /**
     * @throws ValueObjectException
     */
    public function __construct(private int $amountInCents)
    {
        $this->ensureIsValid($amountInCents);
    }

    public function __toString(): string
    {
        return number_format($this->toFloat(), 2, '.', '');
    }

    /**
     * @throws ValueObjectException
     */
    public static function fromFloat(float $amount): self
    {
        $cents = (int) round($amount * 100);
        return new self($cents);
    }

    public function toFloat(): float
    {
        return $this->amountInCents / 100;
    }

    public function value(): int
    {
        return $this->amountInCents;
    }

    public function add(Balance $other): self
    {
        return new self($this->amountInCents + $other->amountInCents);
    }

    /**
     * @throws ValueObjectException
     */
    public function subtract(Balance $other): self
    {
        if ($this->isLessThan($other)) {
            throw new ValueObjectException(
                title: 'Insufficient Balance',
                status: 422,
                detail: "Cannot subtract {$other->toFloat()} from {$this->toFloat()}.",
                extensions: [
                    'error_code' => 'INSUFFICIENT_BALANCE',
                    'timestamp' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
                ]
            );
        }

        return new self($this->amountInCents - $other->amountInCents);
    }

    public function isGreaterThanOrEqual(Balance $other): bool
    {
        return $this->amountInCents >= $other->amountInCents;
    }

    public function isLessThan(Balance $other): bool
    {
        return $this->amountInCents < $other->amountInCents;
    }

    public function equals(Balance $other): bool
    {
        return $this->amountInCents === $other->amountInCents;
    }

    /**
     * @throws ValueObjectException
     */
    private function ensureIsValid(int $value): void
    {
        if ($value < 0) {
            throw new ValueObjectException(
                title: 'Invalid Balance',
                status: 422,
                detail: "Balance cannot be negative. Received: {$value} (in cents).",
                extensions: [
                    'error_code' => 'NEGATIVE_BALANCE',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
    }
}
