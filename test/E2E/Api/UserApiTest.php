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

namespace HyperfTest\E2E\Api;

use HyperfTest\SetupTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @coversNothing
 */
class UserApiTest extends SetupTestCase
{
    protected string $endpoint = '/api/v1/users';

    public function testValidationsStore()
    {
        $data = [];

        $response = $this->post($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'type',
            'title',
            'status',
            'detail',
            'extensions' => [
                'errors',
                'timestamp',
            ],
        ]);
    }

    public function testStoreNewCommonUserSuccessfully()
    {
        $data = [
            'fullName' => 'John Doe',
            'email' => 'john.doe@mail.com',
            'document' => '492.139.570-50',
            'type' => 'COMMON',
            'password' => 'Mudar@123',
        ];

        $response = $this->post($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'fullName',
                'email',
                'document',
                'type',
                'createdAt',
            ],
        ]);
    }

    public function testStoreNewMerchantUserSuccessfully()
    {
        $data = [
            'fullName' => 'John Doe',
            'email' => 'john.doe@mail.com',
            'document' => '492.139.570-50',
            'type' => 'MERCHANT',
            'password' => 'Mudar@123',
        ];

        $response = $this->post($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'fullName',
                'email',
                'document',
                'type',
                'createdAt',
            ],
        ]);
    }
}
