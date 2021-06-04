<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Decorators;

class FinallyResult
{
    /**
     * FinallyResult constructor.
     *
     * @param mixed $content
     */
    public function __construct(
        public mixed $content
    ) {
    }
}
