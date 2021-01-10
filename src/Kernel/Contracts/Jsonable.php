<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use ArrayAccess;

interface Jsonable extends ArrayAccess
{
    public function toJson(): string;
}
