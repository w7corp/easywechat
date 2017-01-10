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
 * MiniProgramServiceProvider.php.
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
use EasyWeChat\MiniProgram\AccessToken;
use EasyWeChat\MiniProgram\MiniProgram;
use EasyWeChat\MiniProgram\Server\Guard;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class MiniProgramServiceProvider.
 */
class MiniProgramServiceProvider implements ServiceProviderInterface
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
        $pimple['mini_program_access_token'] = function ($pimple) {
            $config = $pimple['config']->get('mini_program');

            return new AccessToken(
                $config['app_id'],
                $config['secret'],
                $pimple['cache']
            );
        };

        $pimple['mini_program_encryptor'] = function ($pimple) {
            $config = $pimple['config']->get('mini_program');

            return new Encryptor(
                $config['app_id'],
                $config['token'],
                $config['aes_key']
            );
        };

        $pimple['mini_program'] = function ($pimple) {
            $config = $pimple['config']->get('mini_program');

            $server = new Guard($config['token']);

            $server->debug($pimple['config']['debug']);

            $server->setEncryptor($pimple['mini_program_encryptor']);

            return new MiniProgram(
                $server,
                $pimple['mini_program_access_token'],
                $config
            );
        };
    }
}
