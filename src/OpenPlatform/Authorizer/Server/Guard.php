<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\Server;

use EasyWeChat\Kernel\Server;

class Guard extends Server
{
    /**
     * Get token from OpenPlatform encryptor.
     *
     * @return string
     */
    protected function getToken(): string
    {
        return $this->app['encryptor']->getToken();
    }
}
