<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use Psr\Http\Message\ResponseInterface;

trait InteractWithXmlMessage
{
    /**
     * @param mixed $response
     * @param mixed $message
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function resolveResponse(mixed $response, mixed $message): ResponseInterface
    {
        $response = $this->transformResponse($this->normalizeResponse($response), $message);

        $response->getBody()->rewind();

        return $response;
    }
}
