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

namespace Core\User\Infra\Criteria;

use Core\Shared\Infra\Criteria\CriteriaInterface;

class ByEmailCriteria implements CriteriaInterface
{
    public function __construct(
        readonly string $email,
    ) {
    }

    public function apply(mixed $query): mixed
    {
        return $query->where('email', $this->email);
    }
}
