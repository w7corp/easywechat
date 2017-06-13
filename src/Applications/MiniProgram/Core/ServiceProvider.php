<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram\Core;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple)
    {
        $pimple['access_token'] = function ($pimple) {
            $accessToken = new AccessToken(
                $pimple['config']['mini_program']['app_id'],
                $pimple['config']['mini_program']['secret']
            );
            $accessToken->setCache($pimple['cache']);

            return $accessToken;
        };
    }
}
