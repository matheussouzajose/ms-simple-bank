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

namespace App\Job;

use App\Service\LoggerService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Hyperf\AsyncQueue\Job;
use Hyperf\Context\ApplicationContext;
use Hyperf\Guzzle\ClientFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class SendEmailJob extends Job
{
    private const string BASE_URI = 'https://util.devi.tools/api/v1/';

    private const string AUTH_PATH = 'notify';

    protected int $maxAttempts = 3;

    public function __construct(
        protected object $eventData,
    ) {
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws GuzzleException
     * @throws Exception
     */
    public function handle(): void
    {
        $container = ApplicationContext::getContainer();
        $client = $container->get(ClientFactory::class)->create([
            'base_uri' => self::BASE_URI,
            'timeout' => 5.0,
        ]);

        $logger = $container->get(LoggerService::class);

        try {
            $logger->info('Email job started');

            $response = $client->post(self::AUTH_PATH, [
                'headers' => ['Accept' => 'application/json'],
                'json' => $this->eventData,
            ]);

            $logger->info('Email sent successfully', [
                'status' => $response->getStatusCode(),
            ]);
        } catch (Exception $e) {
            $logger->error('Email job failed', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function fail(Throwable $e): void
    {
        $container = ApplicationContext::getContainer();
        $logger = $container->get(LoggerService::class);

        $logger->error('Email job failed permanently', [
            'error' => $e->getMessage(),
        ]);
    }
}
