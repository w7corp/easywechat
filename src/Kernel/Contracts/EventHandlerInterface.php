<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

interface EventHandlerInterface
{
    public function handle(mixed $payload = null);
}
