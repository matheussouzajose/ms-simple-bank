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

namespace Core\Shared\Domain\Repository;

use Core\Shared\Domain\Entity;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Shared\Infra\Criteria\CriteriaInterface;

interface RepositoryInterface
{
    public function insert(Entity $entity): void;

    public function findById(Uuid $entityId): ?Entity;

    public function findByCriteria(CriteriaInterface $criteria): ?Entity;

    public function update(Entity $entity): void;
}
