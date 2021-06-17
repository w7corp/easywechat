<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Support\UserAgent;
use EasyWeChat\Kernel\Traits\AccessTokenHttpClientDecorator;
use EasyWeChat\Work\Contracts\AccessToken as AccessTokenInterface;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;
use Symfony\Component\HttpClient\HttpClientTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

class HttpClient implements \EasyWeChat\Work\Contracts\HttpClient
{
    use AccessTokenHttpClientDecorator;

    protected array $defaultOptions = [
        'base_uri' => 'https://qyapi.weixin.qq.com/',
    ];
}
