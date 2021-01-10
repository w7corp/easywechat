<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Events;

use EasyWeChat\Kernel\AccessToken;

class AccessTokenRefreshed
{
    public function __construct(
        public AccessToken $accessToken
    ) {}
}
