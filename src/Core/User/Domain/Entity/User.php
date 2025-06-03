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

namespace Core\User\Domain\Entity;

use Core\Shared\Domain\AggregateRoot;
use Core\Shared\Domain\ValueObject\Uuid;
use Core\User\Domain\Enum\UserType;
use Core\User\Domain\ValueObject\Document;
use Core\User\Domain\ValueObject\Email;
use Core\User\Domain\ValueObject\FullName;
use Core\User\Domain\ValueObject\Pwd\Password;
use DateTime;

class User extends AggregateRoot
{
    public function __construct(
        protected Uuid $id,
        protected DateTime $createdAt,
        protected DateTime $updatedAt,
        protected ?DateTime $deletedAt,
        protected FullName $fullName,
        protected Email $email,
        protected Document $document,
        protected UserType $type,
        protected Password $password,
        protected array $walletIds = [],
    ) {
        parent::__construct(id: $id, createdAt: $createdAt);
    }

    public static function create(
        FullName $fullName,
        Email $email,
        Document $document,
        UserType $type,
        Password $password,
    ): self {
        return new self(
            id: new Uuid(),
            createdAt: new DateTime(),
            updatedAt: new DateTime(),
            deletedAt: null,
            fullName: $fullName,
            email: $email,
            document: $document,
            type: $type,
            password: $password,
        );
    }

    public function canTransfer(): bool
    {
        return $this->type->canTransfer();
    }

    public function addWalletId(Uuid $walletId): void
    {
        if (! in_array($walletId, $this->walletIds, true)) {
            $this->walletIds[] = $walletId;
        }
    }

    public function hasWallet(Uuid $walletId): bool
    {
        return in_array($walletId, $this->walletIds);
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->id->value(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deletedAt?->format('Y-m-d H:i:s'),
            'full_name' => $this->fullName->value(),
            'email' => $this->email->value(),
            'document' => $this->document->value(),
            'type' => $this->type->value,
            'password' => $this->password->value(),
        ];
    }
}
