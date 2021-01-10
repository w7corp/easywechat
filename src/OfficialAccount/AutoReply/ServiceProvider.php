<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\AutoReply;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['auto_reply'] = function ($app) {
            return new Client($app);
        };
    }
}
