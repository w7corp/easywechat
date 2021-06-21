<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Contracts;

use Psr\Http\Message\ResponseInterface;

interface Server
{
    public function serve(): ResponseInterface;
}
