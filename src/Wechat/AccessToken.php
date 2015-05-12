<?php

namespace Overtrue\Wechat;

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
    protected $appSecret;

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
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
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
        $appId     = $this->appId;
        $appSecret = $this->appSecret;
        $cache     = $this->cache;
        $cacheKey  = $this->cacheKey;

        return $this->token = $this->cache->get(
            $cacheKey, function ($cacheKey) use ($appId, $appSecret, $cache) {
                $params = array(
                       'appid'      => $appId,
                       'secret'     => $appSecret,
                       'grant_type' => 'client_credential',
                      );
                $http = new Http();

                $token = $http->get(self::API_TOKEN_GET, $params);

                $cache->set($cacheKey, $token['access_token'], $token['expires_in']);

                return $token['access_token'];
            }
        );
    }
}
