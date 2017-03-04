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
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Foundation\ServiceProviders;

use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenPlatform\AuthorizerToken;
use EasyWeChat\OpenPlatform\AccessToken;
use EasyWeChat\OpenPlatform\Components\Authorizer;
use EasyWeChat\OpenPlatform\EventHandlers\Authorized;
use EasyWeChat\OpenPlatform\EventHandlers\UpdateAuthorized;
use EasyWeChat\OpenPlatform\EventHandlers\Unauthorized;
use EasyWeChat\OpenPlatform\EventHandlers\ComponentVerifyTicket;
use EasyWeChat\OpenPlatform\Guard;
use EasyWeChat\OpenPlatform\OpenPlatform;
use EasyWeChat\OpenPlatform\VerifyTicket;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
        $pimple['open_platform_verify_ticket'] = function ($pimple) {
            return new VerifyTicket(
                $pimple['config']['open_platform']['app_id'],
                $pimple['cache']
            );
        };

        $pimple['open_platform_access_token'] = function ($pimple) {
            return new AccessToken(
                $pimple['config']['open_platform']['app_id'],
                $pimple['config']['open_platform']['secret'],
                $pimple['open_platform_verify_ticket'],
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
            $server = new Guard(
                $pimple['config']['open_platform']['token']
            );

            $server->debug($pimple['config']['debug']);

            $server->setEncryptor($pimple['open_platform_encryptor']);
            $server->setContainer($pimple);

            return new OpenPlatform(
                $server,
                $pimple['open_platform_access_token'],
                $pimple['config']['open_platform']
            );
        };

        $pimple['open_platform_authorizer'] = function ($pimple) {
            return new Authorizer(
                $pimple['open_platform_access_token'],
                $pimple['config']['open_platform']
            );
        };

        $pimple['open_platform_authorization'] = function($pimple) {
            return new Authorization(
                $pimple['open_platform_authorizer'],
                $pimple['config']['open_platform']['app_id'],
                $pimple['cache']
            );
        };

        $pimple['open_platform_authorizer_token'] = function ($pimple) {
            return new AuthorizerToken(
                $pimple['config']['open_platform']['app_id'],
                $pimple['open_platform_authorization']
            );
        };

        // Authorization events handlers.
        $pimple['open_platform_handle_component_verify_ticket'] = function($pimple) {
            return new ComponentVerifyTicket($pimple['open_platform_verify_ticket']);
        };
        $pimple['open_platform_handle_authorized'] = function($pimple) {
            return new Authorized($pimple['open_platform_authorization']);
        };
        $pimple['open_platform_handle_updateauthorized'] = function($pimple) {
            return new UpdateAuthorized($pimple['open_platform_authorization']);
        };
        $pimple['open_platform_handle_unauthorized'] = function($pimple) {
            return new Unauthorized($pimple['open_platform_authorization']);
        };
    }

}
