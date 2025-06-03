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

namespace App\Service;

use Psr\Log\LoggerInterface;
use Stringable;

readonly class EmptyLoggerService implements LoggerInterface
{
    public function emergency(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement emergency() method.
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement alert() method.
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement critical() method.
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement error() method.
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement warning() method.
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement notice() method.
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement info() method.
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        // TODO: Implement debug() method.
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        // TODO: Implement log() method.
    }
}
