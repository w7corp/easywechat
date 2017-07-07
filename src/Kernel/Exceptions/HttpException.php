<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel\Exceptions;

/**
 * Class HttpException.
 *
 * @author overtrue <i@overtrue.me>
 */
class HttpException extends Exception
{
    /**
     * @var array
     */
    public $response;

    /**
     * HttpException constructor.
     *
     * @param string $message
     * @param int    $response
     */
    public function __construct($message, $response = null, $code = null)
    {
        parent::__construct($message, $code);

        $this->response = $response;
    }
}
