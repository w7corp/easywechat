<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Events;

use Symfony\Component\HttpFoundation\Response;

class ServerGuardResponseCreated
{
    public function __construct(
        public Response $response
    ) {
    }
}
