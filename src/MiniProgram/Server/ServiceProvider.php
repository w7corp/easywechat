<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\Server;

use EasyWeChat\MiniProgram\Encryptor;
use EasyWeChat\OfficialAccount\Server\Guard;
use EasyWeChat\OfficialAccount\Server\Handlers\EchoStrHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        !isset($app['encryptor']) && $app['encryptor'] = function ($app) {
            return new Encryptor(
                $app['config']['app_id'],
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
