<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Providers;

use EasyWeChatComposer\Extension;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        !isset($pimple['extension']) && $pimple['extension'] = function ($app) {
            return new Extension($app);
        };
    }
}
