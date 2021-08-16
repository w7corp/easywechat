<?php

namespace EasyWeChat\MiniProgram\UrlLink;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $app)
    {
        $app['url_link'] = function ($app) {
            return new Client($app);
        };
    }
}
