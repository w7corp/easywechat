<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Decorators;

class FinallyResult
{
    public function __construct(public mixed $content)
    {
    }
}
