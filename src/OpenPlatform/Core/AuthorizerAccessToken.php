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
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform\Core;

use EasyWeChat\Foundation\Core\AccessToken as BaseAccessToken;
use EasyWeChat\OpenPlatform\Authorizer;

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
     * @var \EasyWeChat\OpenPlatform\Authorizer
     */
    protected $authorizer;

    /**
     * Set authorizer.
     *
     * @param \EasyWeChat\OpenPlatform\Authorizer $authorizer
     */
    public function setAuthorizer(Authorizer $authorizer)
    {
        $this->authorizer = $authorizer;

        return $this;
    }

    /**
     * {@inheritdoc}.
     */
    public function getToken(bool $forceRefresh = false)
    {
        $cached = $this->authorizer->getAccessToken();

        if ($forceRefresh || empty($cached)) {
            $result = $this->getTokenFromServer();
            $this->authorizer->setAccessToken($result[$this->tokenJsonKey], $result['expires_in'] - 1500);

            return $result[$this->tokenJsonKey];
        }

        return $cached;
    }

    /**
     * {@inheritdoc}.
     */
    public function getTokenFromServer()
    {
        return $this->authorizer->getApi()
            ->getAuthorizerToken(
                $this->authorizer->getAppId(),
                $this->authorizer->getRefreshToken()
            );
    }

    /**
     * Return the AuthorizerAppId.
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->authorizer->getAppId();
    }
}
