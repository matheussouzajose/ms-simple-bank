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

namespace HyperfTest;

use Core\Shared\Infra\Persistence\Eloquent\UnitOfWorkEloquent;
use Hyperf\Testing\TestCase;

abstract class SetupTestCase extends TestCase
{
    protected static ?UnitOfWorkEloquent $uow = null;

    public static function setUpBeforeClass(): void
    {
        exec('php bin/hyperf.php migrate:fresh');
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$uow === null) {
            self::$uow = new UnitOfWorkEloquent();
        }

        self::$uow->start();
    }

    protected function tearDown(): void
    {
        self::$uow->rollback();
        exec('php bin/hyperf.php migrate:fresh');
    }
}
