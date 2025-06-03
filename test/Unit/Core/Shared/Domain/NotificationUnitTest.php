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

namespace HyperfTest\Unit\Core\Shared\Domain;

use Core\Shared\Domain\Notification;
use Hyperf\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class NotificationUnitTest extends TestCase
{
    public function testShouldReturnEmptyErrorsArrayInitially()
    {
        $notification = new Notification();
        $errors = $notification->getErrors();

        $this->assertIsArray($errors);
    }

    public function testShouldAddErrorAndReturnInErrorsArray()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'user',
            'message' => 'name is required',
        ]);

        $errors = $notification->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testShouldReturnFalseWhenNoErrorsExist()
    {
        $notification = new Notification();
        $hasErrors = $notification->hasErrors();
        $this->assertFalse($hasErrors);

        $notification->addError([
            'context' => 'user',
            'message' => 'name is required',
        ]);
        $this->assertTrue($notification->hasErrors());
    }

    public function testShouldReturnConcatenatedErrorMessages()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'user',
            'message' => 'name is required',
        ]);
        $notification->addError([
            'context' => 'user',
            'message' => 'description is required',
        ]);
        $message = $notification->messages();

        $this->assertIsString($message);
        $this->assertEquals(
            expected: 'user: name is required,user: description is required,',
            actual: $message
        );
    }

    public function testShouldReturnFilteredErrorMessagesByContext()
    {
        $notification = new Notification();
        $notification->addError([
            'context' => 'user',
            'message' => 'title is required',
        ]);
        $notification->addError([
            'context' => 'category',
            'message' => 'name is required',
        ]);

        $this->assertCount(2, $notification->getErrors());

        $message = $notification->messages(
            context: 'user'
        );
        $this->assertIsString($message);
        $this->assertEquals(
            expected: 'user: title is required,',
            actual: $message
        );
    }
}
