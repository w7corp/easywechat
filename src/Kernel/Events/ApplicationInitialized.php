<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Events;

use EasyWeChat\Kernel\ServiceContainer;

class ApplicationInitialized
{
    public function __construct(
        public ServiceContainer $app
    ) {
    }
}
