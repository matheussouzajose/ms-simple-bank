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

namespace Core\Payment\Infra\Gateway;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Hyperf\Guzzle\ClientFactory;
use Psr\Log\LoggerInterface;

final class HttpAuthorizationService implements AuthorizationGatewayInterface
{
    private const string BASE_URI = 'https://util.devi.tools/api/v2/';

    private const string AUTH_PATH = 'authorize';

    private Client $client;

    public function __construct(
        private readonly ClientFactory $clientFactory,
        private readonly LoggerInterface $logger
    ) {
        $this->client = $this->clientFactory->create([
            'base_uri' => self::BASE_URI,
            'timeout' => 10.0,
        ]);
    }

    public function authorize(): bool
    {
        try {
            $response = $this->client->get(self::AUTH_PATH, [
                'headers' => ['Accept' => 'application/json'],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            return $data['status'] === 'success' && $data['data']['authorization'];
        } catch (RequestException $e) {
            $this->logger->error('Authorization request failed', [
                'url' => self::AUTH_PATH,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
