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
 * Shake.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;



/**
 * 微信摇一摇周边
 */
class Shake
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    const API_GETINFO = 'https://api.weixin.qq.com/shakearound/user/getshakeinfo';


    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }


    /**
     * 获取摇周边的设备及用户信息.
     * http://mp.weixin.qq.com/wiki/12/f18e741c4cab6652afe3878774672de4.html
     * @param $ticket
     * @return bool
     */
    public function shakeinfo($ticket)
    {
        $need_poi = 1;
        $params = array(
                   'ticket' => $ticket,
                   'need_poi' => $need_poi,
                  );

        $response = $this->http->jsonPost(self::API_GETINFO, $params);
        return $response;//['data'];
    }


}
