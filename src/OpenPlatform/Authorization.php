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
use EasyWeChat\Support\Collection;

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
    private $api;

    /**
     * Open Platform App Id, aka, Component App Id.
     *
     * @var string
     */
    private $appId;

    /**
     * Authorizer App Id.
     *
     * @var string
     */
    private $authorizerAppId;

    /**
     * Auth code.
     *
     * @var string
     */
    private $authCode;

    /**
     * Authorization Constructor.
     *
     * Users need not concern the details.
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
     * Sets the auth code.
     *
     * @param $code
     */
    public function setAuthCode($code)
    {
        $this->authCode = $code;
    }

    /**
     * Gets the auth code.
     *
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }

    /**
     * Sets the auth info from the message of the auth event sent by WeChat.
     *
     * @param \EasyWeChat\Support\Collection $message
     */
    public function setFromAuthMessage(Collection $message)
    {
        if ($authorizerAppId = $message->get('AuthorizerAppid')) {
            $this->setAuthorizerAppId($authorizerAppId);
        }
        if ($authorizationCode = $message->get('AuthorizationCode')) {
            $this->setAuthCode($authorizationCode);
        }
    }

    /**
     * Handles authorization: calls the API, saves the tokens.
     *
     * @return Collection
     */
    public function handleAuthorization()
    {
        $info = $this->getAuthorizationInfo();

        $appId = $info['authorization_info']['authorizer_appid'];
        $this->setAuthorizerAppId($appId);

        $this->setAuthorizerAccessToken($info['authorization_info']['authorizer_access_token']);
        $this->setAuthorizerRefreshToken($info['authorization_info']['authorizer_refresh_token']);

        $authorizerInfo = $this->getAuthorizerInfo();
        // Duplicated info.
        $authorizerInfo->forget('authorization_info');
        $info->merge($authorizerInfo->all());

        return $info;
    }

    /**
     * Handles the authorizer access token: calls the API, saves the token.
     *
     * @return string the authorizer access token
     */
    public function handleAuthorizerAccessToken()
    {
        $data = $this->api->getAuthorizerToken(
            $this->getAuthorizerAppId(),
            $this->getAuthorizerRefreshToken()
        );

        $this->setAuthorizerAccessToken($data);

        return $data['authorizer_access_token'];
    }

    /**
     * Gets the authorization information.
     * Like authorizer app id, access token, refresh token, function scope, etc.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizationInfo()
    {
        return $this->api->getAuthorizationInfo($this->getAuthCode());
    }

    /**
     * Gets the authorizer information.
     * Like authorizer name, logo, business, etc.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getAuthorizerInfo()
    {
        return $this->api->getAuthorizerInfo($this->getAuthorizerAppId());
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
        return $this->cache->save($this->getAuthorizerAccessTokenKey(), $token, $expires - 1500);
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
     * @throws Exception when refresh token is not present
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
