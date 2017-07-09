<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Server;

use EasyWeChat\Kernel\Encryptor;
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
    public function register(Container $app)
    {
        $app['encryptor'] = function ($app) {
            return new Encryptor(
                $app['config']['app_id'],
                $app['config']['token'],
                $app['config']['aes_key']
            );
        };

        $app['server'] = function ($app) {
            $server = new Guard($app['config']['token']);
            $server->debug($app['config']['debug']);
            $server->setEncryptor($app['encryptor']);
            $handlers = [
                Guard::EVENT_AUTHORIZED => new Handlers\Authorized(),
                Guard::EVENT_UNAUTHORIZED => new Handlers\Unauthorized(),
                Guard::EVENT_UPDATE_AUTHORIZED => new Handlers\UpdateAuthorized(),
                Guard::EVENT_COMPONENT_VERIFY_TICKET => new Handlers\ComponentVerifyTicket($app['verify_ticket']),
            ];
            $server->setHandlers($handlers);

            return $server;
        };
    }
}
