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
 * Authorization.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    lixiao <leonlx126@gmail.com>
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\Exception;
use EasyWeChat\OpenPlatform\Api\BaseApi;

class Authorization
{
    const CACHE_KEY_ACCESS_TOKEN = 'easywechat.open_platform.authorizer_access_token';
    const CACHE_KEY_REFRESH_TOKEN = 'easywechat.open_platform.authorizer_refresh_token';

    /**
     * Cache.
     *
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * Base API.
     *
     * @var \EasyWeChat\OpenPlatform\Api\BaseApi
     */
    protected $api;

    /**
     * Open Platform App Id, aka, Component App Id.
     *
     * @var string
     */
    protected $appId;

    /**
     * Authorizer App Id.
     *
     * @var string
     */
    protected $authorizerAppId;

    /**
     * Authorization Constructor.
     *
     * @param \EasyWeChat\OpenPlatform\Api\BaseApi $api
     * @param string                               $appId
     * @param \Doctrine\Common\Cache\Cache         $cache
     */
    public function __construct(BaseApi $api, $appId, Cache $cache)
    {
        $this->api = $api;
        $this->appId = $appId;
        $this->cache = $cache;
    }

    /**
     * Gets the base api.
     *
     * @return \EasyWeChat\OpenPlatform\Api\BaseApi
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Sets the authorizer app id.
     *
     * @param string $authorizerAppId
     *
     * @return $this
     */
    public function setAuthorizerAppId($authorizerAppId)
    {
        $this->authorizerAppId = $authorizerAppId;

        return $this;
    }

    /**
     * Gets the authorizer app id, or throws if not found.
     *
     * @return string
     *
     * @throws \EasyWeChat\Core\Exception
     */
    public function getAuthorizerAppId()
    {
        if (!$this->authorizerAppId) {
            throw new Exception(
                'Authorizer App Id is not present, you may not make the authorization yet.'
            );
        }

        return $this->authorizerAppId;
    }

    /**
     * Saves the authorizer access token in cache.
     *
     * @param string $token
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise
     */
    public function setAuthorizerAccessToken($token, $expires = 7200)
    {
        return $this->cache->save($this->getAuthorizerAccessTokenKey(), $token, $expires);
    }

    /**
     * Gets the authorizer access token.
     *
     * @return string
     */
    public function getAuthorizerAccessToken()
    {
        return $this->cache->fetch($this->getAuthorizerAccessTokenKey());
    }

    /**
     * Saves the authorizer refresh token in cache.
     *
     * @param string $refreshToken
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise
     */
    public function setAuthorizerRefreshToken($refreshToken)
    {
        return $this->cache->save($this->getAuthorizerRefreshTokenKey(), $refreshToken);
    }

    /**
     * Gets the authorizer refresh token.
     *
     * @return string
     *
     * @throws \EasyWeChat\Core\Exception when refresh token is not present
     */
    public function getAuthorizerRefreshToken()
    {
        if ($token = $this->cache->fetch($this->getAuthorizerRefreshTokenKey())) {
            return $token;
        }

        throw new Exception(
            'Authorizer Refresh Token is not present, you may not make the authorization yet.'
        );
    }

    /**
     * Removes the authorizer access token from cache.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful
     */
    public function removeAuthorizerAccessToken()
    {
        return $this->cache->delete($this->getAuthorizerAccessTokenKey());
    }

    /**
     * Removes the authorizer refresh token from cache.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful
     */
    public function removeAuthorizerRefreshToken()
    {
        return $this->cache->delete($this->getAuthorizerRefreshTokenKey());
    }

    /**
     * Gets the authorizer access token cache key.
     *
     * @return string
     */
    public function getAuthorizerAccessTokenKey()
    {
        return self::CACHE_KEY_ACCESS_TOKEN.$this->appId.$this->getAuthorizerAppId();
    }

    /**
     * Gets the authorizer refresh token cache key.
     *
     * @return string
     */
    public function getAuthorizerRefreshTokenKey()
    {
        return self::CACHE_KEY_REFRESH_TOKEN.$this->appId.$this->getAuthorizerAppId();
    }
}
