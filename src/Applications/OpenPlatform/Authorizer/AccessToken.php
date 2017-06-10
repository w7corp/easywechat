<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Authorizer;

use EasyWeChat\Applications\Base\Core\AccessToken as BaseAccessToken;
use EasyWeChat\Applications\OpenPlatform\Base\Client;
use EasyWeChat\Exceptions\Exception;

class AccessToken extends BaseAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $tokenJsonKey = 'authorizer_access_token';

    /**
     * Api instance.
     *
     * @var \EasyWeChat\Applications\OpenPlatform\Api\Client
     */
    protected $api;

    /**
     * @var \EasyWeChat\Applications\OpenPlatform\Authorizer
     */
    protected $authorizer;

    /**
     * Authorizer AppId.
     *
     * @var string
     */
    protected $appId;

    /**
     * Authorizer Refresh Token.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * Set the api instance.
     *
     * @param \EasyWeChat\Applications\OpenPlatform\Api\Client $api
     *
     * @return $this
     */
    public function setApi(Client $api)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * Set the authorizer app id.
     *
     * @param string $appId
     *
     * @return $this
     */
    public function setAppId(string $appId)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * Set the authorizer refresh token.
     *
     * @param string $refreshToken
     *
     * @return $this
     */
    public function setRefreshToken(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    public function getTokenFromServer()
    {
        return $this->api->getAuthorizerToken(
            $this->appId, $this->refreshToken
        );
    }

    /**
     * Return the authorizer appId.
     *
     * @throws \EasyWeChat\Exceptions\Exception
     *
     * @return string
     */
    public function getAppId(): string
    {
        if (!$this->appId) {
            throw new Exception('Authorizer App Id is not present, you may not make the authorizer yet.');
        }

        return $this->appId;
    }

    /**
     * {@inheritdoc}.
     */
    public function getCacheKey(): string
    {
        return 'easywechat.open_platform.authorizer_access_token.'.$this->getClientId().$this->appId;
    }
}
