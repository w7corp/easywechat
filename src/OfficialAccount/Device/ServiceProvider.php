<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Device;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @author soone <66812590@qq.com
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['device'] = function ($app) {
            return new Client($app);
        };
    }
}
