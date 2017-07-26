<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Account;

use EasyWeChat\OpenPlatform\Auth\AuthorizerAccessToken;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author Scholer <scholer_l@live.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $accessToken = new AuthorizerAccessToken($app);
        $accessToken->setOpenPlatformAccessToken($app['access_token']);

        $app['account'] = function ($app) use ($accessToken) {
            return new Client($app, $accessToken);
        };
    }
}
