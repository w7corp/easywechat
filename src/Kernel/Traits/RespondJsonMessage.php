<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Message;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait RespondJsonMessage
{
    public function transformJsonToReply(mixed $response, Message $message, ?Encryptor $encryptor = null): ResponseInterface
    {
        if (empty($response)) {
            return new Response(200, [], 'success');
        }

        return $this->createJsonResponse(
            attributes: $this->normalizeJsonResponse($response),
            encryptor: $encryptor
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function normalizeJsonResponse(mixed $response): array
    {
        if (! is_string($response) && is_callable($response)) {
            $response = $response();
        }

        if (is_array($response)) {
            if (! isset($response['msgtype'])) {
                throw new InvalidArgumentException('msgtype cannot be empty.');
            }

            return $response;
        }

        throw new InvalidArgumentException(
            sprintf('Invalid Response type "%s".', gettype($response))
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function createJsonResponse(array $attributes, ?Encryptor $encryptor = null): ResponseInterface
    {
        $jsonStr = json_encode($attributes, JSON_UNESCAPED_UNICODE);

        if (is_string($jsonStr)) {
            return new Response(200, ['Content-Type' => 'application/json'], $encryptor ? $encryptor->encrypt($jsonStr, messageType: $this->messageType) : $jsonStr);
        }

        throw new InvalidArgumentException(
            sprintf('Invalid Response content "%s".', implode(',', $attributes))
        );
    }
}
