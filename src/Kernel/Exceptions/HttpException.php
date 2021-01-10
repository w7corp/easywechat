<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Exceptions;

use Psr\Http\Message\ResponseInterface;

class HttpException extends Exception
{
    public ?\Psr\Http\Message\ResponseInterface $response;

    /**
     * @param  string  $message
     * @param  \Psr\Http\Message\ResponseInterface|null  $response
     * @param  null  $code
     */
    public function __construct(string $message, ResponseInterface $response = null, $code = null)
    {
        parent::__construct($message, $code);

        $this->response = $response;

        if ($response) {
            $response->getBody()->rewind();
        }
    }
}
