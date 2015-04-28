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

        return $this->cache->get($this->cacheKey, function() {
            $params = array(
                       'appid'      => $this->appId,
                       'secret'     => $this->appSecret,
                       'grant_type' => 'client_credential',
                      );
            $http = new Http();

            $token = $http->get(self::API_TOKEN_GET, $params);

            $this->cache->set($this->cacheKey, $token['access_token'], $token['expires_in']);

            return $this->token = $token['access_token'];
        });
    }
}