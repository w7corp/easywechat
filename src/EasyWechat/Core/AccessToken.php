<?php
/**
 * AccessToken.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Core;

/**
 * Class AccessToken
 *
 * @package EasyWeChat\Core
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
     * Cacher
     *
     * @var Cache
     */
    protected $cacher;

    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * token
     *
     * @var string
     */
    protected $token;

    /**
     * Cache key name.
     *
     * @var string
     */
    protected $cacheKey = 'overtrue.wechat.access_token';

    // API
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * Constructor.
     *
     * @param string $appId
     * @param string $secret
     * @param Cache  $cacher
     * @param Http   $http
     */
    public function __construct($appId, $secret, Cache $cacher, Http $http)
    {
        $this->appId  = $appId;
        $this->secret = $secret;
        $this->cacher = $cacher;
        $this->http   = $http;
    }

    /**
     * Get token from WeChat API.
     *
     * @return string
     */
    public function getToken()
    {
        if ($this->token) {
            return $this->token;
        }

        // for php 5.3
        $appId    = $this->appId;
        $secret   = $this->secret;
        $cacher   = $this->cacher;
        $http     = $this->http;
        $cacheKey = $this->cacheKey;
        $api      = self::API_TOKEN_GET;

        return $this->token = $this->cacher->get(
            $cacheKey,
            function ($cacheKey) use ($appId, $secret, $cacher, $http, $api) {
                $params = array(
                    'appid' => $appId,
                    'secret' => $secret,
                    'grant_type' => 'client_credential',
                );

                $token = $http->get($api, $params);

                $cacher->set($cacheKey, $token['access_token'], $token['expires_in']);

                return $token['access_token'];
            }
        );
    }
}//end class
