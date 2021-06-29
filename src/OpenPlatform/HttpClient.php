<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as AccessTokenAwareHttpClientInterface;
use EasyWeChat\Kernel\Traits\AccessTokenAwareHttpClient;

class HttpClient implements AccessTokenAwareHttpClientInterface
{
    use AccessTokenAwareHttpClient;

    protected array $defaultOptions = [
        'base_uri' => 'https://api.weixin.qq.com/',
    ];
}
