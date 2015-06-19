<?php
/**
 * AccessToken.php
 *
 * Part of MasApi\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace MasApi\Wechat;

/**
 * 全局通用 AccessToken
 */
class AccessToken
{

    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $masAccessToken;

    /**
     * 缓存类
     *
     * @var Cache
     */
    protected $cache;

    /**
     * token
     *
     * @var string
     */
    protected $token;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $cacheKey = 'overtrue.wechat.access_token';

    // API
    const API_TOKEN_GET = 'https://prism-dev.masengine.com/app/index.php/Api/getWxAccessToken';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $masAccessToken
     */
    public function __construct($appId, $masAccessToken)
    {
        $this->appId     = $appId;
        $this->masAccessToken = $masAccessToken;
        $this->cache     = new Cache($appId);
    }

    /**
     * 缓存 setter
     *
     * @param Cache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * 获取Token
     *
     * @return string
     */
    public function getToken()
    {
        if ($this->token) {
            return $this->token;
        }

        // for php 5.3
        $appId       = $this->appId;
        $masAccessToken   = $this->masAccessToken;
        $cache       = $this->cache;
        $cacheKey    = $this->cacheKey;
        $apiTokenGet = self::API_TOKEN_GET;

        return $this->token = $this->cache->get(
            $cacheKey,
            function ($cacheKey) use ($appId, $masAccessToken, $cache, $apiTokenGet) {
                $params = array(
                           'appid'      => $appId,
                           'access_token'     => $masAccessToken,
                           // 'grant_type' => 'client_credential',
                          );
                $http = new Http();

                $token = $http->get($apiTokenGet, $params);

                $cache->set($cacheKey, $token['access_token'], $token['expires_in']);

                return $token['access_token'];
            }
        );
    }
}
