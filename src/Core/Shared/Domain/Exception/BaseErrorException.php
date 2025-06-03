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

namespace Core\Shared\Domain\Exception;

use Core\Shared\Traits\MethodsMagicsTrait;
use Exception;

abstract class BaseErrorException extends Exception
{
    use MethodsMagicsTrait;

    protected string $type;

    protected string $title;

    protected int $status;

    protected ?string $detail;

    protected ?string $instance;

    protected array $extensions;

    public function __construct(
        string $title = 'An error occurred',
        int $status = 500,
        ?string $detail = null,
        ?string $type = 'about:blank',
        ?string $instance = null,
        array $extensions = []
    ) {
        parent::__construct($title, $status);

        $this->type = $type;
        $this->title = $title;
        $this->status = $status;
        $this->detail = $detail;
        $this->instance = $instance;
        $this->extensions = $extensions;
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'type' => $this->type,
            'title' => $this->title,
            'status' => $this->status,
            'detail' => $this->detail,
            'instance' => $this->instance,
            'extensions' => ! empty($this->extensions) ? $this->extensions : null,
        ]);
    }
}
