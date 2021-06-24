<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Support\Xml;
use Psr\Http\Message\ResponseInterface;

class ServerResponse extends Response
{
    public const SUCCESS_EMPTY_RESPONSE = 'SUCCESS';

    public static function success(string $body = self::SUCCESS_EMPTY_RESPONSE): ResponseInterface
    {
        return new self(200, [], $body);
    }

    public static function failed($remark, $status = 200, $headers = []): ResponseInterface
    {
        return new self($status, $headers, $remark);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public static function xml(array $attributes, ?Encryptor $encryptor = null): ServerResponse
    {
        $xml = Xml::build($attributes);

        if ($encryptor) {
            $time = $attributes['CreateTime'] ?? \time();
            $nonce = $attributes['nonce'] ?? \uniqid();

            $xml = Xml::build(
                [
                    'MsgType' => $attributes['MsgType'] ?? 'text',
                    'Encrypt' => $encryptor->encrypt($xml, $nonce, $time),
                    'TimeStamp' => $time,
                    'Nonce' => $nonce,
                ]
            );
        }

        return new self(200, ['Content-Type' => 'application/xml'], $xml);
    }
}
