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

namespace Core\Wallet\Domain\Entity;

use Core\Shared\Domain\AggregateRoot;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Wallet\Domain\ValueObject\Balance;
use DateTime;

class Wallet extends AggregateRoot
{
    public function __construct(
        protected Uuid $id,
        protected DateTime $createdAt,
        protected DateTime $updatedAt,
        protected ?DateTime $deletedAt,
        protected Uuid $userId,
        protected Balance $balance,
    ) {
        parent::__construct(id: $id, createdAt: $createdAt);
    }

    public static function create(
        Uuid $userId,
        Balance $balance,
    ): self {
        return new self(
            id: new Uuid(),
            createdAt: new DateTime(),
            updatedAt: new DateTime(),
            deletedAt: null,
            userId: $userId,
            balance: $balance,
        );
    }

    /**
     * @throws ValueObjectException
     */
    public function debit(Balance $amount): void
    {
        $this->balance = $this->balance->subtract($amount);
    }

    public function credit(Balance $amount): void
    {
        $this->balance = $this->balance->add($amount);
    }

    public function hasSufficientBalance(Balance $amount): bool
    {
        return $this->balance->isGreaterThanOrEqual($amount);
    }

    public function toArray(): array
    {
        return [
            'wallet_id' => $this->id->value(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
            'user_id' => $this->userId->value(),
            'balance' => $this->balance->value(),
        ];
    }
}
