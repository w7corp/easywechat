<?php
/**
 * OAuthServiceProvider.php
 *
 * This file is part of the wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\WeChat\ServiceProviders;

use Overtrue\Socialite;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


/**
 * Class OAuthServiceProvider.
 */
class OAuthServiceProvider implements ServiceProviderInterface
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
        $pimple['oauth'] = $pimple->factory(function($pimple){
            $callback = $this->prepareCallbackUrl($pimple);
            $scopes   = $pimple['config']->get('oauth.scopes', []);

            $socialite = (new Socialite(
                [
                    'wechat' => [
                        $pimple['config']['app_id'],
                        $pimple['config']['secret'],
                        $callback,
                    ],
                ],
            ))->driver('wechat');


            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite;
        });
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
        $baseUrl  = $pimple['request']->getSchemeAndHttpHost();
        $callback = stripos($callback, 'http') === 0 ? $callback : $baseUrl .'/'.ltrim($callback, '/');

        return $callback;
    }
}