<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use ArrayAccess;

/**
 * @extends ArrayAccess<string, mixed>
 */
interface Jsonable extends ArrayAccess
{
    public function toJson(): string;
}
