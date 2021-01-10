<?php

declare(strict_types=1);

namespace EasyWeChat\Work\GroupRobot;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $app)
    {
        $app['group_robot'] = function ($app) {
            return new Client($app);
        };

        $app['group_robot_messenger'] = function ($app) {
            return new Messenger($app['group_robot']);
        };
    }
}
