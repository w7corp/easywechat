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
}
