<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram\Server;

use EasyWeChat\Applications\MiniProgram\Encryption\Encryptor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['encryptor'] = function ($container) {
            return new Encryptor(
                $container['config']['mini_program']['app_id'],
                $container['config']['mini_program']['token'],
                $container['config']['mini_program']['aes_key']
            );
        };

        $app['server'] = function ($container) {
            $server = new Guard($container['config']['mini_program']['token']);
            $server->debug($container['config']['debug']);
            $server->setEncryptor($container['encryptor']);

            return $server;
        };
    }
}
