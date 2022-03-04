<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponseValidator
{
    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException if the response is not successful.
     */
    public function validate(ResponseInterface $response): void;
}
