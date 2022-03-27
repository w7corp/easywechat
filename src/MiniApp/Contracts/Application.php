<?php

declare(strict_types=1);

namespace EasyWeChat\MiniApp\Contracts;

use EasyWeChat\Kernel\Contracts\AccessToken;
use EasyWeChat\Kernel\Contracts\Config;
use EasyWeChat\Kernel\Contracts\Server;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

interface Application
{
    public function getAccount(): Account;

    public function getEncryptor(): Encryptor;

    public function getServer(): Server;

    public function getRequest(): ServerRequestInterface;

    public function getClient(): AccessTokenAwareClient;

    public function getHttpClient(): HttpClientInterface;

    public function getConfig(): Config;

    public function getAccessToken(): AccessToken;

    public function getCache(): CacheInterface;
}
