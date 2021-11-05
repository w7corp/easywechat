<?php

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Support\Xml;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

trait RespondXmlMessage
{
    public function transformResponse(array $response, Message $message, ?Encryptor $encryptor): ResponseInterface
    {
        return $this->createXmlResponse(
            attributes: array_filter(
                \array_merge(
                    [
                        'ToUserName' => $message->FromUserName,
                        'FromUserName' => $message->ToUserName,
                        'CreateTime' => \time(),
                    ],
                    $response
                )
            ),
            encryptor: $encryptor
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function normalizeResponse(mixed $response): array
    {
        if (\is_array($response)) {
            if (!isset($response['MsgType'])) {
                throw new InvalidArgumentException('MsgType cannot be empty.');
            }

            return $response;
        }

        if (is_string($response) || is_numeric($response)) {
            return [
                'MsgType' => 'text',
                'Content' => $response,
            ];
        }

        throw new InvalidArgumentException(
            sprintf('Invalid Response type "%s".', gettype($response))
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function createXmlResponse(array $attributes, ?Encryptor $encryptor = null): ResponseInterface
    {
        $xml = Xml::build($attributes);

        if ($encryptor) {
            $time = $attributes['CreateTime'] ?? \time();
            $nonce = $attributes['nonce'] ?? \uniqid();

            $xml = $encryptor->encrypt($xml, $nonce, $time);
        }

        return new Response(200, ['Content-Type' => 'application/xml'], $xml);
    }
}
