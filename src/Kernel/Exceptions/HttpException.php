<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Exceptions;

use Psr\Http\Message\ResponseInterface;

class HttpException extends Exception
{
    public ?ResponseInterface $response;

    /**
     * HttpException constructor.
     */
    public function __construct(string $message, ?ResponseInterface $response = null, int $code = 0)
    {
        parent::__construct($message, $code);

        $this->response = $response;

        if ($response) {
            $response->getBody()->rewind();
        }
    }
}
