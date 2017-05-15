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
 * AuthorizerAccessToken.php.
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

namespace EasyWeChat\OpenPlatform\Core;

use EasyWeChat\Exceptions\Exception;
use EasyWeChat\Foundation\Core\AccessToken as BaseAccessToken;
use EasyWeChat\OpenPlatform\Api\BaseApi;

/**
 * Class AuthorizerAccessToken.
 *
 * AuthorizerAccessToken is responsible for the access token of the authorizer,
 * the complexity is that this access token also requires the refresh token
 * of the authorizer which is acquired by the open platform authorizer process.
 *
 * This completely overrides the original AccessToken.
 */
class AuthorizerAccessToken extends BaseAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $tokenJsonKey = 'authorizer_access_token';

    /**
     * Api instance
     *
     * @var \EasyWeChat\OpenPlatform\Api\BaseApi
     */
    protected $api;

    /**
     * @var \EasyWeChat\OpenPlatform\Authorizer
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
     * @param \EasyWeChat\OpenPlatform\Api\BaseApi $api
     *
     * @return $this
     */
    public function setApi(BaseApi $api)
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
