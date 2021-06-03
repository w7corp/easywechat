<?php

namespace EasyWeChat\Kernel\Server;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response extends HttpResponse
{
    public const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function success(): HttpResponse
    {
        return new HttpResponse(self::SUCCESS_EMPTY_RESPONSE);
    }

    /**
     * @param string $xml
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function reply(string $xml): HttpResponse
    {
        return new HttpResponse($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
