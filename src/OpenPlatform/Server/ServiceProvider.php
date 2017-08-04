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

class ServiceProvider implements ServiceProviderInterface
{
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
            $server = (new Guard($app))->debug($app['config']['debug']);
            $handlers = [
                Guard::EVENT_AUTHORIZED => new Handlers\Authorized($app),
                Guard::EVENT_UNAUTHORIZED => new Handlers\Unauthorized($app),
                Guard::EVENT_UPDATE_AUTHORIZED => new Handlers\UpdateAuthorized($app),
                Guard::EVENT_COMPONENT_VERIFY_TICKET => new Handlers\VerifyTicketRefreshed($app),
            ];

            return $server->setHandlers($handlers);
        };
    }
}
