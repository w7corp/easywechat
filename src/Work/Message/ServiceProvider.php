<?php

declare(strict_types=1);

namespace EasyWeChat\Work\Message;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['message'] = function ($app) {
            return new Client($app);
        };

        $app['messenger'] = function ($app) {
            $messenger = new Messenger($app['message']);

            if (is_int($app['config']['agent_id'])) {
                $messenger->ofAgent($app['config']['agent_id']);
            }

            return $messenger;
        };
    }
}
