<?php

declare(strict_types=1);

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
            return new Guard($app);
        };
    }
}
