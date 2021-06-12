<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationInterface;
use EasyWeChat\OfficialAccount\Contracts\Response as ResponseInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class Response extends GuzzleResponse implements ResponseInterface
{
    public const SUCCESS_EMPTY_RESPONSE = 'success';

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
    public static function replay(
        array $attributes,
        ApplicationInterface $application,
        array $appends = [],
    ): Response {
        $xml = XML::build($attributes);

        if ($application->getRequest()->isSafeMode()) {
            $time = $appends['time'] ?? \time();
            $nonce = $appends['nonce'] ?? \uniqid();

            $xml = XML::build(
                [
                    'MsgType' => $attributes['MsgType'] ?? 'text',
                    'Encrypt' => $application->getEncryptor()->encrypt($xml, $nonce, $time),
                    'TimeStamp' => $time,
                    'Nonce' => $nonce,
                ]
            );
        }

        return new self(200, ['Content-Type' => 'application/xml'], $xml);
    }
}
