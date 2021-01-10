<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\ServerGuard;

class Guard extends ServerGuard
{
    /**
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return !is_null($this->app['request']->get('echostr'));
    }
}
