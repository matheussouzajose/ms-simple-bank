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

namespace Core\Payment\Application\UseCase\Transfer;

readonly class TransferInput
{
    public function __construct(
        public string $payerId,
        public string $payeeId,
        public int $amount,
    ) {
    }
}
