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
                    'redirect' => $this->prepareCallbackUrl($app),
                ],
            ], $app['request']))->driver('wework');

            $scopes = (array) $app['config']->get('oauth.scopes', ['snsapi_base']);

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite->setAccessToken(new AccessTokenDelegate($app));
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $app
     *
     * @return string
     */
    private function prepareCallbackUrl($app)
    {
        $callback = $app['config']->get('oauth.callback');

        if (0 === stripos($callback, 'http')) {
            return $callback;
        }

        $baseUrl = $app['request']->getSchemeAndHttpHost();

        return $baseUrl.'/'.ltrim($callback, '/');
    }
}
