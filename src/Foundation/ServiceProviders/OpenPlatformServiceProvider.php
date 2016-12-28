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
 * OpenPlatformServiceProvider.php.
 *
 * This file is part of the wechat.
 *
 * (c) mingyoung <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Guard;
use EasyWeChat\OpenPlatform\OpenPlatform;
use EasyWeChat\OpenPlatform\VerifyTicket;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class BroadcastServiceProvider.
 */
class OpenPlatformServiceProvider implements ServiceProviderInterface
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
        $pimple['open_platform_access_token'] = function ($pimple) {
            return new AccessToken(
                $pimple['config']['open_platform']['app_id'],
                $pimple['config']['open_platform']['secret'],
                $pimple['cache']
            );
        };

        $pimple['component_verify_ticket'] = function ($pimple) {
            return new VerifyTicket(
                $pimple['config']['open_platform'],
                $pimple['cache']
            );
        };

        $pimple['open_platform_encryptor'] = function ($pimple) {
            return new Encryptor(
                $pimple['config']['open_platform']['app_id'],
                $pimple['config']['open_platform']['token'],
                $pimple['config']['open_platform']['aes_key']
            );
        };

        $pimple['open_platform'] = function ($pimple) {
            $server = new Guard($pimple['config']['open_platform']['token']);

            $server->debug($pimple['config']['debug']);

            $server->setEncryptor($pimple['open_platform_encryptor']);

            return new OpenPlatform(
                $server,
                $pimple['open_platform_access_token'],
                $pimple['config']['open_platform'],
                $pimple['component_verify_ticket']
            );
        };
    }
}
