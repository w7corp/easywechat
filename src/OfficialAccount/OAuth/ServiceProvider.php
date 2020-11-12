<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\OAuth;

use Overtrue\Socialite\SocialiteManager as Socialite;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['oauth'] = function ($app) {
            $wechat = [
                'wechat' => [
                    'client_id' => $app['config']['app_id'],
                    'client_secret' => $app['config']['secret'],
                    'redirect' => $this->prepareCallbackUrl($app),
                ],
            ];

            if (!empty($app['config']['component_app_id'] && !empty($app['config']['component_app_token']))) {
                $wechat['wechat']['component'] = [
                    'id' => $app['config']['component_app_id'],
                    'token' => $app['config']['token'],
                ] ;
            }

            $socialite = (new Socialite($wechat))->create('wechat');

            $scopes = (array)$app['config']->get('oauth.scopes', ['snsapi_userinfo']);

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite;
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

        return $baseUrl . '/' . ltrim($callback, '/');
    }
}
