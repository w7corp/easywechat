<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as AccessTokenAwareHttpClientInterface;
use EasyWeChat\Kernel\Traits\AccessTokenAwareHttpClient;

class HttpClient implements AccessTokenAwareHttpClientInterface
{
    use AccessTokenAwareHttpClient;

    protected array $defaultOptions = [
        'base_uri' => 'https://qyapi.weixin.qq.com/',
    ];
}
