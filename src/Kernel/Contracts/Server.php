<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Contracts;

use EasyWeChat\Kernel\Message;
use Psr\Http\Message\ResponseInterface;

interface Server
{
    public function serve(): ResponseInterface;

    public function transformResponse(array $response, Message $message): ResponseInterface;
}
