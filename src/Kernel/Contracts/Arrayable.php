<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use ArrayAccess;

/**
 * @extends \ArrayAccess<mixed, mixed>
 */
interface Arrayable extends ArrayAccess
{
    /**
     * @return array<int|string, mixed>
     */
    public function toArray(): array;
}
