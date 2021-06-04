<?php

namespace EasyWeChat\Kernel\Server;

use EasyWeChat\Kernel\Support\XML;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response extends HttpResponse
{
    public const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function success(): HttpResponse
    {
        //todo: transform to psr response
        return new HttpResponse(self::SUCCESS_EMPTY_RESPONSE);
    }

    //TODO: 可能需要迁移为 factory 或者放入 Application / Account
    public static function reply(array $attributes, bool $encrypt = false)
    {
        $xml = XML::build($attributes);

        if ($encrypt) {
            $time = \time();
            $nonce = \uniqid();
            $xml = XML::build(
                [
                    'MsgType' => $attributes['MsgType'] ?? 'text',
                    'Encrypt' => $this->encryptor->encrypt($xml, $time, $nonce),
                    'TimeStamp' => $time,
                    'Nonce' => $nonce,
                ]
            );
        }

        return new HttpResponse($xml);
    }
}
