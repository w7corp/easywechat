<?php

namespace EasyWeChat\Kernel\Server;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response
{
    public const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * @param string $content
     * @param int    $status
     * @param array  $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function send(
        string $content,
        int $status = 200,
        array $headers = []
    ): HttpResponse
    {
        return new HttpResponse($content, $status, $headers);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function success(): HttpResponse
    {
        return new HttpResponse(self::SUCCESS_EMPTY_RESPONSE);
    }
}
