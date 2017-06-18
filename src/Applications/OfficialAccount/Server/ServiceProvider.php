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
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @see      https://github.com/overtrue/wechat
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Applications\OfficialAccount\Server;

use EasyWeChat\Applications\OfficialAccount\Encryption\Encryptor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['encryptor'] = function ($container) {
            return new Encryptor(
                $container['config']['app_id'],
                $container['config']['token'],
                $container['config']['aes_key']
            );
        };

        $app['server'] = function ($container) {
            $server = new Guard($container['config']['token']);

            $server->debug($container['config']['debug']);

            $server->setEncryptor($container['encryptor']);

            return $server;
        };
    }
}
