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
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2017
 *
 * @see      https://github.com/overtrue/wechat
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Applications\OpenPlatform\Server;

use EasyWeChat\Applications\OpenPlatform\Encryption\Encryptor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['open_platform.encryptor'] = function ($container) {
            return new Encryptor(
                $container['config']['open_platform']['app_id'],
                $container['config']['open_platform']['token'],
                $container['config']['open_platform']['aes_key']
            );
        };

        $container['open_platform.handlers.component_verify_ticket'] = function ($container) {
            return new Handlers\ComponentVerifyTicket($container['open_platform.verify_ticket']);
        };
        $container['open_platform.handlers.authorized'] = function () {
            return new Handlers\Authorized();
        };
        $container['open_platform.handlers.updateauthorized'] = function () {
            return new Handlers\UpdateAuthorized();
        };
        $container['open_platform.handlers.unauthorized'] = function () {
            return new Handlers\Unauthorized();
        };

        $container['open_platform.server'] = function ($container) {
            $server = new Guard($container['config']['open_platform']['token']);
            $server->debug($container['config']['debug']);
            $server->setEncryptor($container['open_platform.encryptor']);
            $server->setHandlers([
                Guard::EVENT_AUTHORIZED => $container['open_platform.handlers.authorized'],
                Guard::EVENT_UNAUTHORIZED => $container['open_platform.handlers.unauthorized'],
                Guard::EVENT_UPDATE_AUTHORIZED => $container['open_platform.handlers.updateauthorized'],
                Guard::EVENT_COMPONENT_VERIFY_TICKET => $container['open_platform.handlers.component_verify_ticket'],
            ]);

            return $server;
        };
    }
}
