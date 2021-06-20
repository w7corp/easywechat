<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Contracts;

use Psr\Http\Message\ResponseInterface;

interface Server
{
    public function process(): ResponseInterface;
}
