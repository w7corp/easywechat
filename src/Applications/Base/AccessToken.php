<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\Base;

use EasyWeChat\Exceptions\HttpException;
use EasyWeChat\Support;

/**
 * Class AccessToken.
 *
 * @author overtrue <i@overtrue.me>
 */
class AccessToken
{
    use Support\HasHttpRequests,
        Support\InteractsWithCache;

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
    protected $jsonKey = 'access_token';

    /**
     * Constructor.
     *
     * @param string      $clientId
     * @param string|null $clientSecret
     */
    final public function __construct(string $clientId, string $clientSecret = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Get the current clientId.
     *
     * @return string
     */
    final public function getClientId(): string
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
    final public function getClientSecret()
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
     * Return the API request form fields.
     *
     * @return array
     */
    public function requestFields(): array
    {
        return [
            'appid' => $this->clientId,
            'secret' => $this->clientSecret,
            'grant_type' => 'client_credential',
        ];
    }

    /**
     * Get token from cache.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken(bool $forceRefresh = false): string
    {
        $cached = $this->getCache()->get($this->getCacheKey());

        if ($forceRefresh || is_null($cached)) {
            $result = $this->getTokenFromServer();
            $this->setToken($token = $result[$this->jsonKey], $result['expires_in']);

            return $token;
        }

        return $cached;
    }

    /**
     * Set token.
     *
     * @param string $token
     * @param int    $expires
     *
     * @return $this
     */
    public function setToken(string $token, int $expires = 7200)
    {
        // XXX: T_T... 7200 - 1500
        $this->getCache()->set($this->getCacheKey(), $token, $expires - 1500);

        return $this;
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
        $result = $this->parseJSON($this->get(static::API_TOKEN_GET, $this->requestFields()));

        if (empty($result[$this->jsonKey])) {
            throw new HttpException('Request AccessToken fail. Response: '.json_encode($result, JSON_UNESCAPED_UNICODE));
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
    public function getQueryName(): string
    {
        return $this->queryName;
    }

    /**
     * Return the API request queries.
     *
     * @return array
     */
    public function getQueryFields(): array
    {
        return [$this->queryName => $this->getToken()];
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
    public function getCacheKey(): string
    {
        if (is_null($this->cacheKey)) {
            return $this->prefix.$this->clientId;
        }

        return $this->cacheKey;
    }
}
