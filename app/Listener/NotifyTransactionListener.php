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

namespace App\Listener;

use App\Job\SendEmailJob;
use Core\Payment\Domain\Event\TransactionCreatedIntegrationEvent;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;

#[Listener]
class NotifyTransactionListener implements ListenerInterface
{
    protected DriverFactory $driver;

    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            TransactionCreatedIntegrationEvent::class,
        ];
    }

    public function process(object $event): void
    {
        $this->driver = $this->container->get(DriverFactory::class);
        $this->driver->get('default')->push(
            new SendEmailJob(
                eventData: $event,
            )
        );
    }
}
