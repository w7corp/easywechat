<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\Server;

use EasyWeChat\Applications\OpenPlatform\Encryption\Encryptor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['open_platform.encryptor'] = function ($container) {
            return new Encryptor(
                $container['config']['client_id'],
                $container['config']['token'],
                $container['config']['aes_key']
            );
        };

        $container['open_platform.handlers'] = function ($container) {
            return [
                Guard::EVENT_AUTHORIZED => new Handlers\Authorized(),
                Guard::EVENT_UNAUTHORIZED => new Handlers\Unauthorized(),
                Guard::EVENT_UPDATE_AUTHORIZED => new Handlers\UpdateAuthorized(),
                Guard::EVENT_COMPONENT_VERIFY_TICKET => new Handlers\ComponentVerifyTicket($container['open_platform.verify_ticket']),
            ];
        };

        $container['open_platform.server'] = function ($container) {
            $server = new Guard($container['config']['token']);
            $server->debug($container['config']['debug']);
            $server->setEncryptor($container['open_platform.encryptor']);
            $server->setHandlers($container['open_platform.handlers']);

            return $server;
        };
    }
}
