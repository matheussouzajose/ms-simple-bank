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

namespace Core\Payment\Infra\Persistence\Eloquent;

use App\Model\Transaction as TransactionModel;
use Core\Payment\Domain\Repository\TransactionRepositoryInterface;
use Core\Shared\Domain\Entity;
use Core\Shared\Domain\Exception\InfraException;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\Shared\Infra\Criteria\CriteriaInterface;
use DateTimeImmutable;
use Exception;

class TransactionEloquentRepository implements TransactionRepositoryInterface
{
    public function __construct(protected TransactionModel $model, protected UnitOfWorkInterface $unitOfWork)
    {
    }

    public function insert(Entity $entity): void
    {
        $this->model->create($entity->toArray());
        $this->unitOfWork->addEntity($entity);
    }

    public function findById(Uuid $entityId): ?Entity
    {
        $transaction = $this->model->find($entityId->value());
        return $transaction ? TransactionModelMapper::toEntity($transaction) : null;
    }

    public function findByCriteria(CriteriaInterface $criteria): ?Entity
    {
        $query = $criteria->apply($this->model);
        $user = $query->first();
        return $user ? TransactionModelMapper::toEntity($user) : null;
    }

    /**
     * @throws Exception
     */
    public function update(Entity $entity): void
    {
        if (! $entityDb = $this->model->find($entity->id->value())) {
            throw new InfraException(
                title: 'Transaction Not Found',
                status: 404,
                detail: 'The requested user could not be located in the system.',
                extensions: [
                    'error_code' => 'TRANSACTION_NOT_FOUND',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
        $entityDb->update($entity->toArray());
        $entityDb->refresh();
    }
}
