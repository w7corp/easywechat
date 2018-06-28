<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\OAuth;

use Overtrue\Socialite\SocialiteManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['oauth'] = function ($app) {
            $socialite = (new SocialiteManager([
                'wework' => [
                    'client_id' => $app['config']['corp_id'],
                    'client_secret' => null,
                    'redirect' => null,
                ],
            ], $app['request']))->driver('wework');

            $scopes = (array) $app['config']->get('oauth.scopes', ['snsapi_base']);

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite->setAccessToken(new AccessTokenDelegate($app));
        };
    }
}
