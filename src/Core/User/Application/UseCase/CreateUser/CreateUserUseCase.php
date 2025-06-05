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

namespace Core\User\Application\UseCase\CreateUser;

use Core\Shared\Domain\Exception\ApplicationException;
use Core\Shared\Domain\Exception\ValueObjectException;
use Core\Shared\Domain\Repository\UnitOfWorkInterface;
use Core\User\Application\UseCase\Common\UserOutput;
use Core\User\Domain\Entity\User;
use Core\User\Domain\Enum\UserType;
use Core\User\Domain\Repository\UserRepositoryInterface;
use Core\User\Domain\ValueObject\Document;
use Core\User\Domain\ValueObject\Email;
use Core\User\Domain\ValueObject\FullName;
use Core\User\Domain\ValueObject\Pwd\PasswordFactory;
use Core\User\Infra\Criteria\ByDocumentCriteria;
use Core\User\Infra\Criteria\ByEmailCriteria;
use Core\Wallet\Domain\Entity\Wallet;
use Core\Wallet\Domain\Repository\WalletRepositoryInterface;
use Core\Wallet\Domain\ValueObject\Balance;
use DateTimeImmutable;
use Throwable;

class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly WalletRepositoryInterface $walletRepository,
        private readonly UnitOfWorkInterface $unitOfWork,
    ) {
    }

    /**
     * @throws ValueObjectException
     * @throws ApplicationException
     * @throws Throwable
     */
    public function execute(CreateUserInput $input): UserOutput
    {
        $this->assertUserDoesNotExist($input);

        $user = User::create(
            fullName: new FullName($input->fullName),
            email: new Email($input->email),
            document: new Document($input->document),
            type: UserType::from($input->type),
            password: PasswordFactory::create('bcrypt', $input->password),
        );

        $wallet = Wallet::create(
            userId: $user->id,
            balance: new Balance($input->balance ?? 0),
        );
        $user->addWalletId($wallet->id);

        $this->unitOfWork->do(function () use ($user, $wallet) {
            $this->userRepository->insert($user);
            $this->walletRepository->insert($wallet);
        });

        return UserOutput::toOutput($user);
    }

    /**
     * @throws ApplicationException
     */
    private function assertUserDoesNotExist(CreateUserInput $input): void
    {
        if ($this->userRepository->findByCriteria(new ByEmailCriteria($input->email))) {
            throw new ApplicationException(
                title: 'User Already Exists',
                status: 422,
                detail: "A user with the email '{$input->email}' already exists.",
                extensions: [
                    'error_code' => 'USER_EMAIL_CONFLICT',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }

        if ($this->userRepository->findByCriteria(new ByDocumentCriteria($input->document))) {
            throw new ApplicationException(
                title: 'User Already Exists',
                status: 422,
                detail: "A user with the document '{$input->document}' already exists.",
                extensions: [
                    'error_code' => 'USER_DOCUMENT_CONFLICT',
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ]
            );
        }
    }
}
