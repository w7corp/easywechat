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
 * AccessToken.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Foundation\Core;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\FilesystemCache;
use EasyWeChat\Exceptions\HttpException;
use EasyWeChat\OfficialAccount\Core\Http;

abstract class AccessToken
{
    // use HasHttpRequests;
    // use Concerns\InteractsWithCache;

    /**
     * Client Id (AppId, CorpId).
     *
     * @var string
     */
    protected $clientId;

    /**
     * Client Secret (AppSecret, CorpSecret).
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * Query name.
     *
     * @var string
     */
    protected $queryName = 'access_token';

    /**
     * Response Json key name.
     *
     * @var string
     */
    protected $tokenJsonKey = 'access_token';

    /**
     * Cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Cache Key.
     *
     * @var string
     */
    protected $cacheKey;

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Constructor.
     *
     * @param string      $clientId
     * @param string|null $clientSecret
     */
    final public function __construct(string $clientId, ? string $clientSecret = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get the current clientId.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set the clientId associated with the instance.
     *
     * @param string $clientId
     *
     * @return $this
     */
    public function setClientId(string $clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get the current clientSecret.
     *
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Set the clientSecret associated with the instance.
     *
     * @param string $clientSecret
     *
     * @return $this
     */
    public function setClientSecret(string $clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * Return the app id.
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->getClientId();
    }

    /**
     * Return the secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->getClientSecret();
    }

    /**
     * Get token from cache.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken(? bool $forceRefresh = false)
    {
        $cached = $this->getCache()->fetch($this->getCacheKey());

        if ($forceRefresh || empty($cached)) {
            $result = $this->getTokenFromServer();
            $this->setToken($result[$this->tokenJsonKey], $result['expires_in']);

            return $result[$this->tokenJsonKey];
        }

        return $cached;
    }

    /**
     * 设置自定义 token.
     *
     * @param string $token
     * @param int    $expires
     *
     * @return $this
     */
    public function setToken(string $token, ? int $expires = 7200)
    {
        // XXX: T_T... 7200 - 1500
        $this->getCache()->save($this->getCacheKey(), $token, $expires - 1500);

        return $this;
    }

    /**
     * Return the API request form fields.
     *
     * @return array
     */
    public function requestFields()
    {
        return [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'grant_type' => 'client_credential',
        ];
    }

    /**
     * Get the access token from WeChat server.
     *
     * @throws \EasyWeChat\Exceptions\HttpException
     *
     * @return array
     */
    public function getTokenFromServer()
    {
        $http = $this->getHttp();

        $result = $http->parseJSON($http->get(static::API_TOKEN_GET, $this->requestFields()));

        if (empty($result[$this->tokenJsonKey])) {
            throw new HttpException('Request AccessToken fail. response: '.json_encode($result, JSON_UNESCAPED_UNICODE));
        }

        return $result;
    }

    /**
     * Set the query name.
     *
     * @param string $queryName
     *
     * @return $this
     */
    public function setQueryName(string $queryName)
    {
        $this->queryName = $queryName;

        return $this;
    }

    /**
     * Return the query name.
     *
     * @return string
     */
    public function getQueryName()
    {
        return $this->queryName;
    }

    /**
     * Return the API request queries.
     *
     * @return array
     */
    public function getQueryFields()
    {
        return [$this->queryName => $this->getToken()];
    }

    /**
     * Set cache instance.
     *
     * @param \Doctrine\Common\Cache\Cache $cache
     *
     * @return AccessToken
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Return the cache manager.
     *
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        return $this->cache ?: $this->cache = new FilesystemCache(sys_get_temp_dir());
    }

    /**
     * Return the http instance.
     *
     * @return \EasyWeChat\OfficialAccount\Core\Http
     */
    public function getHttp()
    {
        return $this->http ?: $this->http = new Http();
    }

    /**
     * Set the http instance.
     *
     * @param \EasyWeChat\OfficialAccount\Core\Http $http
     *
     * @return $this
     */
    public function setHttp(Http $http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Set the access token prefix.
     *
     * @param string $prefix
     *
     * @return $this
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set access token cache key.
     *
     * @param string $cacheKey
     *
     * @return $this
     */
    public function setCacheKey(string $cacheKey)
    {
        $this->cacheKey = $cacheKey;

        return $this;
    }

    /**
     * Get access token cache key.
     *
     * @return string
     */
    public function getCacheKey()
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix.$this->clientId;
        }

        return $this->cacheKey;
    }
}
