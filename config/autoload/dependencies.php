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
use Core\Payment\Domain\Repository\TransactionRepositoryInterface;
use Core\Payment\Infra\Gateway\AuthorizationGatewayInterface;
use Core\Payment\Infra\Gateway\HttpAuthorizationService;
use Core\Payment\Infra\Persistence\Eloquent\TransactionEloquentRepository;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Core\Shared\Infra\Persistence\Eloquent\UnitOfWorkEloquent;
use Core\User\Domain\Repository\UserRepositoryInterface;
use Core\User\Infra\Persistence\Eloquent\UserEloquentRepository;
use Core\Wallet\Domain\Repository\WalletRepositoryInterface;
use Core\Wallet\Infra\Persistence\Eloquent\WalletEloquentRepository;

/*
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    UnitOfWorkInterface::class => UnitOfWorkEloquent::class,
    UserRepositoryInterface::class => UserEloquentRepository::class,
    WalletRepositoryInterface::class => WalletEloquentRepository::class,
    TransactionRepositoryInterface::class => TransactionEloquentRepository::class,
    AuthorizationGatewayInterface::class => HttpAuthorizationService::class,
];
