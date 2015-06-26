<?php

/**
 * Color.php.
 *
 * Part of EasyWeChat.
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

namespace EasyWeChat\Card;

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
     * <pre>
     * $config:
     *
     * array(
     *  'app_id' => YOUR_APPID,  // string mandatory;
     *  'secret' => YOUR_SECRET, // string mandatory;
     * )
     * </pre>
     *
     * @param array $config configuration array
     */
    public function __construct(array $config)
    {
        $this->http = new Http(new AccessToken($config));
        $this->cache = new Cache($config['app_id']);
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
