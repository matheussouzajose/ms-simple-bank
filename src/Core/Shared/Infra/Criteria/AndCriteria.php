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

namespace Core\Shared\Infra\Criteria;

class AndCriteria implements CriteriaInterface
{
    private array $criteria;

    public function __construct(array $criteria)
    {
        $this->criteria = $criteria;
    }

    public function apply(mixed $query): mixed
    {
        foreach ($this->criteria as $criterion) {
            $query = $criterion->apply($query);
        }
        return $query;
    }
}
