<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Events;

use Psr\Http\Message\ResponseInterface;

class HttpResponseCreated
{
    public function __construct(
        public ResponseInterface $response
    ) {
    }
}
