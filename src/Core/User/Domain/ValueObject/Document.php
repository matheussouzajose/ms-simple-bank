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

final class Document extends ValueObject
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
        return preg_replace('/[^0-9]/', '', $this->value);
    }

    protected function isEqualTo(ValueObject $other): bool
    {
        return $other instanceof self && $this->value() === $other->value();
    }

    /**
     * @throws ValueObjectException
     */
    private function ensureIsValid(string $document): void
    {
        if (! $this->isValidCpf($document) && ! $this->isValidCnpj($document)) {
            throw new ValueObjectException(
                title: 'Invalid Document Format',
                status: 422,
                detail: "The provided document \"{$document}\" is not valid. Please provide a valid document.",
                extensions: [
                    'error_code' => 'INVALID_DOCUMENT',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
    }

    private function isValidCpf(string $cpf): bool
    {
        return v::cpf()->isValid($cpf);
    }

    private function isValidCnpj(string $cnpj): bool
    {
        return v::cnpj()->isValid($cnpj);
    }
}
