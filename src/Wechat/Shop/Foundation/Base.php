<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Base.php.
 *
 * Part of Overtrue\Wechat\Shop\Foundation.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 *
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop\Foundation;

use Overtrue\Wechat\Http;

class Base
{
    /**
     * @var object Http
     */
    protected $http;

    /**
     * @var array|bool
     */
    protected $response;

    /**
     * 初始化.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    /**
     * 获得响应.
     *
     * @param array $response
     *
     * @return bool|array
     *
     * @throws ShopsException
     */
    protected function getResponse($response = array())
    {
        $response = empty($response) ? $this->response : $response;

        if ($response['errcode'] == 0) {
            if (count($response) == 2) {
                return true;
            }
            if (count($response) > 2) {
                if (isset($response['errmsg'])) {
                    unset($response['errmsg']);
                }
                if (isset($response['errcode'])) {
                    unset($response['errcode']);
                }

                if (count($response) == 1) {
                    $key = array_keys($response);

                    return $response[$key[0]];
                } else {
                    return $response;
                }
            }
        } else {
            throw new ShopsException($response['errmsg'], $response['errcode']);
        }
    }
}
