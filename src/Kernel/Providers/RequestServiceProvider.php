<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        !isset($pimple['request']) && $pimple['request'] = function () {
            return Request::createFromGlobals();
        };
    }
}
