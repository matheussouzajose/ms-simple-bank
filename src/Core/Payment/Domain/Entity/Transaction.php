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

namespace Core\Payment\Domain\Entity;

use Core\Payment\Domain\Event\TransactionCreatedEvent;
use Core\Shared\Domain\AggregateRoot;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Wallet\Domain\ValueObject\Balance;
use DateTime;

class Transaction extends AggregateRoot
{
    public function __construct(
        protected Uuid $id,
        protected DateTime $createdAt,
        protected Uuid $payerId,
        protected Uuid $payeeId,
        protected Balance $amount
    ) {
        parent::__construct(id: $id, createdAt: $createdAt);
    }

    public static function create(
        Uuid $payerId,
        Uuid $payeeId,
        Balance $amount
    ): self {
        $transaction = new self(
            id: new Uuid(),
            createdAt: new DateTime(),
            payerId: $payerId,
            payeeId: $payeeId,
            amount: $amount
        );
        $transaction->applyEvent(new TransactionCreatedEvent($transaction->id));
        return $transaction;
    }

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->id->value(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'payer_id' => $this->payerId->value(),
            'payee_id' => $this->payeeId->value(),
            'amount' => $this->amount->value(),
        ];
    }
}
