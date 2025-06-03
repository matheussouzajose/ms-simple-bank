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

namespace Core\Shared\Domain;

abstract class ValueObject
{
    public function equals(self $other): bool
    {
        if (get_class($other) !== get_class($this)) {
            return false;
        }

        return $this->isEqualTo($other);
    }

    abstract protected function isEqualTo(ValueObject $other): bool;
}
