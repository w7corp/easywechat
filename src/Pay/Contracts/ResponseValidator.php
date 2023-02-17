<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Contracts;

use EasyWeChat\Kernel\HttpClient\Response;
use Psr\Http\Message\ResponseInterface;

interface ResponseValidator
{
    public function validate(ResponseInterface|Response $response): void;
}
