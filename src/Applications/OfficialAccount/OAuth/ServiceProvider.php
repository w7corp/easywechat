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
 * ServiceProvider.php.
 *
 * This file is part of the wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\OAuth;

use Overtrue\Socialite\SocialiteManager as Socialite;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['oauth'] = function ($pimple) {
            $callback = $this->prepareCallbackUrl($pimple);
            $scopes = $pimple['config']->get('oauth.scopes', []);
            $socialite = (new Socialite(
                [
                    'wechat' => [
                        'client_id' => $pimple['config']['app_id'],
                        'client_secret' => $pimple['config']['secret'],
                        'redirect' => $callback,
                    ],
                ]
            ))->driver('wechat');

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite;
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $pimple
     *
     * @return string
     */
    private function prepareCallbackUrl($pimple)
    {
        $callback = $pimple['config']->get('oauth.callback');
        if (0 === stripos($callback, 'http')) {
            return $callback;
        }
        $baseUrl = $pimple['request']->getSchemeAndHttpHost();

        return $baseUrl.'/'.ltrim($callback, '/');
    }
}
