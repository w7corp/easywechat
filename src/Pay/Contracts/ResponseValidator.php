<?php

namespace EasyWeChat\Pay\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponseValidator
{
    public function validate(ResponseInterface $response): bool;
}
