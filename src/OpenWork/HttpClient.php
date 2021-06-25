<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Traits\AccessTokenAwareHttpClient;

class HttpClient implements \EasyWeChat\OpenWork\Contracts\HttpClient
{
    use AccessTokenAwareHttpClient;

    protected array $defaultOptions = [
        'base_uri' => 'https://qyapi.weixin.qq.com/',
    ];
}
