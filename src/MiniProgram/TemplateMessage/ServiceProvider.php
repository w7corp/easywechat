<?php

declare(strict_types=1);

namespace EasyWeChat\MiniProgram\TemplateMessage;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['template_message'] = function ($app) {
            return new Client($app);
        };
    }
}
