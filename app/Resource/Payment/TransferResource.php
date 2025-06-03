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

namespace App\Resource\Payment;

use Hyperf\Resource\Json\JsonResource;

class TransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'payer' => [
                'id' => $this->payer->id,
                'fullName' => $this->payer->fullName,
                'email' => $this->payer->email,
            ],
            'payee' => [
                'id' => $this->payee->id,
                'fullName' => $this->payee->fullName,
                'email' => $this->payee->email,
            ],
            'amount' => $this->amount,
            'createdAt' => $this->createdAt,
        ];
    }
}
// id: $transaction->id->value(),
// payer: UserOutput::toOutput($payer),
// payee: UserOutput::toOutput($payee),
// amount: $transaction->amount->value(),
// createdAt: $transaction->createdAt->format('Y-m-d H:i:s'),
