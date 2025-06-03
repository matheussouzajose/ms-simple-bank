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

namespace Core\Wallet\Application\UseCase\Credit;

readonly class CreditWalletInput
{
    public function __construct(
        readonly string $userId,
        readonly string $walletId,
        readonly int $balance,
    ) {
    }
}
