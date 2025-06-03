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
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

use function Hyperf\Support\env;

$isTesting = env('APP_ENV') === 'testing';

/*
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'default' => [
        'handlers' => $isTesting
            ? [
                [
                    'class' => NullHandler::class,
                    'constructor' => [
                        'level' => Logger::DEBUG,
                    ],
                ],
            ]
            : [
                [
                    'class' => StreamHandler::class,
                    'constructor' => [
                        'stream' => 'php://stdout',
                        'level' => env('LOG_LEVEL', Logger::DEBUG),
                    ],
                    'formatter' => [
                        'class' => JsonFormatter::class,
                    ],
                ],
            ],
    ],
];
