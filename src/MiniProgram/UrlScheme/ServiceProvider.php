<?php

namespace EasyWeChat\MiniProgram\UrlScheme;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $app['url_scheme'] = function ($app) {
            return new Client($app);
        };
    }
}
