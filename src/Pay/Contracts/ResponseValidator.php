<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Contracts;

use EasyWeChat\Kernel\Exceptions\BadResponseException;
use Psr\Http\Message\ResponseInterface;

interface ResponseValidator
{
    /**
     * @throws BadResponseException if the response is not successful.
     */
    public function validate(ResponseInterface $response): void;
}
