<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform;

use EasyWeChat\Applications\OfficialAccount\Application as OfficialAccount;
use EasyWeChat\Applications\OpenPlatform;
use EasyWeChat\Support\ServiceContainer;

/**
 * Class Application.
 *
 * @property \EasyWeChat\Applications\OpenPlatform\Server\Guard $server
 * @property \EasyWeChat\Applications\OpenPlatform\Core\AccessToken $access_token
 * @property \EasyWeChat\Applications\OpenPlatform\PreAuthorization\Client $pre_authorization
 *
 * @method \EasyWeChat\Support\Collection|array getAuthorizationInfo(string $authCode = null)
 * @method \EasyWeChat\Support\Collection|array getAuthorizerInfo(string $authorizerAppId)
 * @method \EasyWeChat\Support\Collection|array getAuthorizerOption(string $authorizerAppId, string $optionName)
 * @method \EasyWeChat\Support\Collection|array setAuthorizerOption(string $authorizerAppId, string $optionName, string $optionValue)
 */
class Application extends ServiceContainer
{
    protected $providers = [
        OpenPlatform\Core\ServiceProvider::class,
        OpenPlatform\Base\ServiceProvider::class,
        OpenPlatform\Server\ServiceProvider::class,
        OpenPlatform\Authorizer\ServiceProvider::class,
        OpenPlatform\PreAuthorization\ServiceProvider::class,
    ];

    /**
     * Create an instance of OfficialAccount.
     *
     * @param string $appId
     * @param string $refreshToken
     *
     * @return \EasyWeChat\Applications\officialAccount\Application
     */
    public function createOfficialAccount(string $appId, string $refreshToken)
    {
        $instance = new OfficialAccount([/** todo */]);

        $instance['oauth'] = $this->offsetGet('open_platform.oauth');
        $instance['server'] = $this->offsetGet('open_platform.server');
        $instance['access_token'] = $this->offsetGet('open_platform.authorizer_access_token')->setAppId($appId)->setRefreshToken($refreshToken);

        return $instance;
    }

    /**
     * Quick access to the base-api.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->api, $method], $args);
    }
}
