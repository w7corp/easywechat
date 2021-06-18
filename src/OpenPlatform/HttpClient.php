<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Traits\AccessTokenHttpClientDecorator;

class HttpClient implements \EasyWeChat\OpenPlatform\Contracts\HttpClient
{
    use AccessTokenHttpClientDecorator;

    protected array $defaultOptions = [
        'base_uri' => 'https://api.weixin.qq.com/',
    ];
}
