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

use EasyWeChat\Applications\Base\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $jsonKey = 'authorizer_access_token';

    /**
     * Component ClientId.
     *
     * @var string
     */
    protected $componentClientId;

    /**
     * Authorizer Refresh Token.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * Set the component ClientId.
     *
     * @param string $value
     */
    public function setComponentClientId(string $value)
    {
        $this->componentClientId = $value;

        return $this;
    }

    /**
     * Set the authorizer refresh token.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setRefreshToken(string $value)
    {
        $this->refreshToken = $value;

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    public function getTokenFromServer()
    {
        $params = [
            'component_appid' => $this->componentClientId,
            'authorizer_appid' => $this->clientId,
            'authorizer_refresh_token' => $this->refreshToken,
        ];

        return $this->parseJSON('json', ['https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token', $params]);
    }

    /**
     * {@inheritdoc}.
     */
    public function getCacheKey(): string
    {
        return 'easywechat.open_platform.authorizer_access_token.'.$this->getClientId().$this->appId;
    }
}
