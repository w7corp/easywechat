<?php

namespace EasyWeChat\Kernel\Traits;

use function array_merge;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\Support\Xml;
use function is_array;
use function is_callable;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use function time;

trait RespondXmlMessage
{
    /**
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function transformToReply(mixed $response, Message $message, ?Encryptor $encryptor = null): ResponseInterface
    {
        if (empty($response)) {
            return new Response(200, [], 'success');
        }

        return $this->createXmlResponse(
            attributes: array_filter(
                array_merge(
                    [
                        'ToUserName' => $message->FromUserName,
                        'FromUserName' => $message->ToUserName,
                        'CreateTime' => time(),
                    ],
                    $this->normalizeResponse($response),
                )
            ),
            encryptor: $encryptor
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws InvalidArgumentException
     */
    protected function normalizeResponse(mixed $response): array
    {
        if (is_callable($response)) {
            $response = $response();
        }

        if (is_array($response)) {
            if (! isset($response['MsgType'])) {
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
     * @param  array<string, mixed>  $attributes
     *
     * @throws RuntimeException
     */
    protected function createXmlResponse(array $attributes, ?Encryptor $encryptor = null): ResponseInterface
    {
        $xml = Xml::build($attributes);

        return new Response(200, ['Content-Type' => 'application/xml'], $encryptor ? $encryptor->encrypt($xml) : $xml);
    }
}
