<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\Server;

use EasyWeChat\Kernel\ServerGuard;

class Guard extends ServerGuard
{
    /**
     * Get token from OpenPlatform encryptor.
     *
     * @return string
     */
    protected function getToken()
    {
        return $this->app['encryptor']->getToken();
    }
}
