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

namespace App\Controller\User;

use App\Request\User\StoreUserRequest;
use App\Resource\User\UserResource;
use Core\Shared\Domain\Exception\ApplicationException;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\User\Application\UseCase\CreateUser\CreateUserInput;
use Core\User\Application\UseCase\CreateUser\CreateUserUseCase;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Throwable;

#[Controller(prefix: 'api/v1/users')]
readonly class UserController
{
    public function __construct(protected CreateUserUseCase $createUserUseCase)
    {
    }

    /**
     * @throws ValueObjectException
     * @throws ApplicationException
     * @throws Throwable
     */
    #[PostMapping(path: '')]
    public function store(StoreUserRequest $request): UserResource
    {
        $data = $request->validated();
        $input = new CreateUserInput(
            fullName: $data['fullName'],
            email: $data['email'],
            document: $data['document'],
            type: $data['type'],
            password: $data['password'],
            balance: 0,
        );
        $output = $this->createUserUseCase->execute(input: $input);
        return (new UserResource($output))
            ->withStatus(201);
    }
}
