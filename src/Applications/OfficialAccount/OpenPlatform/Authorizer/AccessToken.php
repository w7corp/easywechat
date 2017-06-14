<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\OpenPlatform\Authorizer;

use EasyWeChat\Applications\Base\AccessToken as BaseAccessToken;
use EasyWeChat\Support\HasHttpRequests;

class AccessToken extends BaseAccessToken
{
    use HasHttpRequests;

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
     * @param string $clientId
     *
     * @return $this
     */
    public function setComponentClientId(string $clientId)
    {
        $this->componentClientId = $clientId;

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
        $params = [
            'component_appid' => $this->componentClientId,
            'authorizer_appid' => $this->clientId,
            'authorizer_refresh_token' => $this->refreshToken,
        ];

        return $this->parseJSON(
            $this->postJson('https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token', $params)
        );
    }

    /**
     * {@inheritdoc}.
     */
    public function getCacheKey(): string
    {
        return 'easywechat.authorizer_access_token.'.$this->componentClientId.$this->getClientId();
    }
}
