<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Exceptions;

use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;

class BadResponseException extends Exception
{
    #[Pure]
    public function __construct(string $message, public ResponseInterface $response)
    {
        parent::__construct($message);
    }
}
