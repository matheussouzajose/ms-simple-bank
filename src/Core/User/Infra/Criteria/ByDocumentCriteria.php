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

class ByDocumentCriteria implements CriteriaInterface
{
    public function __construct(
        readonly string $document,
    ) {
    }

    public function apply(mixed $query): mixed
    {
        return $query->where('document', preg_replace('/[^0-9]/', '', $this->document));
    }
}
