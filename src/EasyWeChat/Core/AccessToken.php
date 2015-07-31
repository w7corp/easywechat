<?php

/**
 * AccessToken.php.
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

namespace EasyWeChat\Core;

use EasyWeChat\Cache\Manager as Cache;

/**
 * Class AccessToken.
 */
class AccessToken
{
    /**
     * App ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * App secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'easywechat.common.access_token.';

    // API
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * Constructor.
     *
     * @param string $appId
     * @param string $secret
     * @param Cache  $cache
     * @param Http   $http
     */
    public function __construct($appId, $secret, Cache $cache, Http $http)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->cache = $cache;
        $this->http = $http;
    }

    /**
     * Get token from WeChat API.
     *
     * @return string
     */
    public function getToken()
    {
        $cacheKey = $this->prefix.$this->appId;

        return $this->cache->get(
            $cacheKey,
            function ($cacheKey) {
                $params = [
                    'appid' => $this->appId,
                    'secret' => $this->secret,
                    'grant_type' => 'client_credential',
                ];
                $token = $this->http->get(self::API_TOKEN_GET, $params);

                $this->cache->set($cacheKey, $token['access_token'], $token['expires_in'] - 100);

                return $token['access_token'];
            }
        );
    }
}
