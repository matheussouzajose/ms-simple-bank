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

namespace App\Exception\Handler;

use Core\Shared\Domain\Exception\BaseErrorException;
use DateTimeImmutable;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response): MessageInterface|ResponseInterface
    {
        $this->stopPropagation();

        // BaseErrorException format
        if ($throwable instanceof BaseErrorException) {
            $payload = array_filter([
                'type' => $throwable->getCode() ? "https://httpstatuses.com/{$throwable->getCode()}" : $throwable->type ?? 'about:blank',
                'title' => $throwable->title ?? 'An error occurred',
                'status' => $throwable->getCode() ?: 500,
                'detail' => $throwable->detail,
                'instance' => $throwable->instance ?? null,
                'extensions' => ! empty($throwable->extensions) ? $throwable->extensions : null,
            ]);

            $payload['extensions']['timestamp'] = (new DateTimeImmutable())->format(DATE_ATOM);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($throwable->getCode() ?: 500)
                ->withBody(new SwooleStream(json_encode($payload, JSON_UNESCAPED_UNICODE)));
        }

        // ValidationException (422)
        if ($throwable instanceof ValidationException) {
            $status = 422;

            $payload = [
                'type' => 'https://httpstatuses.com/422',
                'title' => 'Validation Error',
                'status' => $status,
                'detail' => 'The request contains invalid data.',
                'extensions' => [
                    'errors' => $throwable->validator->errors(),
                    'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
                ],
            ];

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status)
                ->withBody(new SwooleStream(json_encode($payload, JSON_UNESCAPED_UNICODE)));
        }

        // Fallback: erro genérico (500 ou código mapeado)
        $status = method_exists($throwable, 'getStatusCode') ? $throwable->getStatusCode() : 500;
        $title = $this->mapTitle($status);
        $type = "https://httpstatuses.com/{$status}";
        $detail = $throwable->getMessage() ?: $title;

        $payload = [
            'type' => $type,
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'extensions' => [
                'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
            ],
        ];

        $this->logger->error(sprintf('%s [%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        $this->logger->error($throwable->getTraceAsString());

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status)
            ->withBody(new SwooleStream(json_encode($payload, JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }

    private function mapTitle(int $status): string
    {
        return match ($status) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Route Not Found',
            405 => 'Method Not Allowed',
            422 => 'Validation Error',
            default => 'Internal Server Error',
        };
    }
}
