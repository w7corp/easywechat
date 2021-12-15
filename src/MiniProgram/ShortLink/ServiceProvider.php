<?php

namespace EasyWeChat\MiniProgram\ShortLink;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['short_link'] = function ($app) {
            return new Client($app);
        };
    }
}
