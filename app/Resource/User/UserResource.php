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

namespace App\Resource\User;

use Hyperf\Context\Context;
use Hyperf\Resource\Json\JsonResource;
use Psr\Http\Message\ServerRequestInterface;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'document' => $this->document,
            'type' => $this->type,
            'createdAt' => $this->createdAt,
        ];
    }
}
