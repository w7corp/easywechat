<?php

declare(strict_types=1);

namespace EasyWeChat\Work\MsgAudit;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['msg_audit'] = function ($app) {
            return new Client($app);
        };
    }
}
