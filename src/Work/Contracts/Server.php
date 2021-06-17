<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Contracts;

use Psr\Http\Message\ResponseInterface;

interface Server
{
    public function process(): ResponseInterface;
}
