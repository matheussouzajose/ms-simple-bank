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

use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

readonly class LoggerService
{
    public function __construct(private LoggerFactory $loggerFactory)
    {
    }

    public function info(string $message, array $context = [], string $channel = 'default'): void
    {
        $this->getLogger($channel)->info($message, $context);
    }

    public function error(string $message, array $context = [], string $channel = 'error'): void
    {
        $this->getLogger($channel)->error($message, $context);
    }

    public function warning(string $message, array $context = [], string $channel = 'default'): void
    {
        $this->getLogger($channel)->warning($message, $context);
    }

    public function debug(string $message, array $context = [], string $channel = 'default'): void
    {
        $this->getLogger($channel)->debug($message, $context);
    }

    private function getLogger(string $channel): LoggerInterface
    {
        return $this->loggerFactory->get($channel);
    }
}
