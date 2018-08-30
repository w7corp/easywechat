<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenWork\Server;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\OpenWork\Server\Handlers\EchoStrHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * ServiceProvider.
 *
 * @author xiaomin <keacefull@gmail.com>
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        //微信第三方在校验url是使用的是GET方式请求和corp_id进行加密
        !isset($app['encryptor_corp']) && $app['encryptor_corp'] = function ($app) {
            return new Encryptor(
                $app['config']['corp_id'],
                $app['config']['token'],
                $app['config']['aes_key']
            );
        };

        //微信第三方推送数据时使用的是suite_id进行加密
        !isset($app['encryptor']) && $app['encryptor'] = function ($app) {
            return new Encryptor(
                $app['config']['suite_id'],
                $app['config']['token'],
                $app['config']['aes_key']
            );
        };

        !isset($app['server']) && $app['server'] = function ($app) {
            $guard = new Guard($app);
            $guard->push(new EchoStrHandler($app));

            return $guard;
        };
    }
}
