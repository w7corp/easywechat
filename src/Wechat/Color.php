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
 * Color.php.
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
 * 颜色接口.
 */
class Color
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * Cache对象
     *
     * @var Cache
     */
    protected $cache;

    const API_LIST = 'https://api.weixin.qq.com/card/getcolors';

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
        $this->cache = new Cache($appId);
    }

    /**
     * 获取颜色列表.
     *
     * @return array
     */
    public function lists()
    {
        $key = 'overtrue.wechat.colors';

        // for php 5.3
        $http = $this->http;
        $cache = $this->cache;
        $apiList = self::API_LIST;

        return $this->cache->get(
            $key,
            function ($key) use ($http, $cache, $apiList) {
                $result = $http->get($apiList);

                $cache->set($key, $result['colors'], 86400);// 1 day

                return $result['colors'];
            }
        );
    }
}
