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

namespace App\Controller\Wallet;

use App\Request\Wallet\CreditWalletRequest;
use Core\Shared\Domain\Exception\ApplicationException;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Wallet\Application\UseCase\Credit\CreditWalletInput;
use Core\Wallet\Application\UseCase\Credit\CreditWalletUseCase;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;

#[Controller(prefix: 'api/v1/wallets')]
readonly class WalletController
{
    public function __construct(protected CreditWalletUseCase $debitUseCase)
    {
    }

    /**
     * @throws ValueObjectException
     * @throws ApplicationException
     */
    #[PostMapping(path: 'credit')]
    public function credit(CreditWalletRequest $request, ResponseInterface $response): \Psr\Http\Message\ResponseInterface
    {
        $data = $request->validated();
        $input = new CreditWalletInput(
            userId: $data['userId'],
            walletId: $data['walletId'],
            balance: $data['balance'],
        );
        $this->debitUseCase->execute(input: $input);
        return $response->withStatus(204);
    }
}
