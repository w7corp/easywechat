<?php

namespace EasyWeChat\Kernel;

use Nyholm\Psr7\Response;

class ServerResponse extends Response
{
    public function __construct(
        int $status = 200,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        string $reason = null
    ) {
        parent::__construct($status, $headers, $body, $version, $reason);

        $this->getBody()->rewind();
    }
}
