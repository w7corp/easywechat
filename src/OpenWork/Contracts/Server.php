<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Contracts;

use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\ServerResponse;
use Psr\Http\Message\ResponseInterface;

interface Server
{
    public function serve(): ResponseInterface;

    public function transformResponse(array $response, Message $message): ServerResponse;
}
