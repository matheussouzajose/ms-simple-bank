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

namespace App\Controller\Payment;

use App\Request\Payment\TransferRequest;
use App\Resource\Payment\TransferResource;
use Core\Payment\Application\UseCase\Transfer\TransferInput;
use Core\Payment\Application\UseCase\Transfer\TransferUseCase;
use Core\Shared\Domain\Exception\ApplicationException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Throwable;

#[Controller(prefix: 'api/v1/transfer')]
class TransactionController
{
    public function __construct(protected TransferUseCase $useCase)
    {
    }

    /**
     * @throws Throwable
     * @throws ApplicationException
     */
    #[PostMapping(path: '')]
    public function transfer(TransferRequest $request): TransferResource
    {
        $data = $request->validated();
        $input = new TransferInput(payerId: $data['payerId'], payeeId: $data['payeeId'], amount: $data['value']);
        $output = $this->useCase->execute(input: $input);
        return new TransferResource($output);
    }
}
