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

namespace Core\Wallet\Infra\Criteria;

use Core\Shared\Domain\ValueObject\Uuid;
use Core\Shared\Infra\Criteria\CriteriaInterface;

readonly class ByUserIdCriteria implements CriteriaInterface
{
    public function __construct(
        public Uuid $userId,
    ) {
    }

    public function apply(mixed $query): mixed
    {
        return $query->where('user_id', $this->userId->value());
    }
}
