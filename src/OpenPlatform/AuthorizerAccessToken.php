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

namespace EasyWeChat\OpenPlatform;

// Don't change the alias name please. I met the issue "name already in use"
// when used in Laravel project, not sure what is causing it, this is quick
// solution.
use EasyWeChat\Core\AccessToken as BaseAccessToken;

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
     * @var \EasyWeChat\OpenPlatform\Authorizer
     */
    protected $authorizer;

    /**
     * AuthorizerAccessToken constructor.
     *
     * @param string                              $appId
     * @param \EasyWeChat\OpenPlatform\Authorizer $authorizer
     */
    public function __construct($appId, Authorizer $authorizer)
    {
        parent::__construct($appId, null);

        $this->authorizer = $authorizer;
    }

    /**
     * Get token from WeChat API.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cached = $this->authorizer->getAccessToken();

        if ($forceRefresh || empty($cached)) {
            return $this->renewAccessToken();
        }

        return $cached;
    }

    /**
     * Refresh authorizer access token.
     *
     * @return string
     */
    protected function renewAccessToken()
    {
        $token = $this->authorizer->getApi()
            ->getAuthorizerToken(
                $this->authorizer->getAppId(),
                $this->authorizer->getRefreshToken()
            );

        $this->authorizer->setAccessToken($token['authorizer_access_token'], $token['expires_in'] - 1500);

        return $token['authorizer_access_token'];
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
