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
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Card;

use EasyWeChat\Cache\Adapters\AdapterInterface as Cache;
use EasyWeChat\Core\Http;

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
     * Constructor.
     *
     * @param Http  $http
     * @param Cache $cache
     */
    public function __construct(Http $http, Cache $cache)
    {
        $this->http = $http;
        $this->cache = $cache;
    }

    /**
     * 获取颜色列表.
     *
     * @return array
     */
    public function lists()
    {
        $key = 'overtrue.wechat.colors';

        return $this->cache->get(
            $key,
            function ($key) {
                $result = $this->http->get(self::API_LIST);

                $this->cache->set($key, $result['colors'], 86400); // 1 day

                return $result['colors'];
            }
        );
    }
}
