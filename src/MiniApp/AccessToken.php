<?php

declare(strict_types=1);

namespace EasyWeChat\MiniApp;

class AccessToken extends \EasyWeChat\OfficialAccount\AccessToken
{
    const CACHE_KEY_PREFIX = 'mini_app';
}
