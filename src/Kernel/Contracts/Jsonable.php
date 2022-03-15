<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

interface Jsonable
{
    public function toJson(): string|false;
}
