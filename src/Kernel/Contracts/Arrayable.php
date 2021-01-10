<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use ArrayAccess;

interface Arrayable extends ArrayAccess
{
    public function toArray(): array;
}
