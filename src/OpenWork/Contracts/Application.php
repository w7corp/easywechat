<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork\Contracts;

use EasyWeChat\Kernel\Contracts\AccessToken;
use EasyWeChat\Kernel\UriBuilder;
use EasyWeChat\Kernel\Contracts\Config;
use EasyWeChat\Kernel\Encryptor;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;

interface Application
{
    public function getAccount(): Account;

    public function getEncryptor(): Encryptor;

    public function getServer(): Server;

    public function getRequest(): ServerRequestInterface;

    public function getClient(): UriBuilder;

    public function getHttpClient(): HttpClient;

    public function getConfig(): Config;

    public function getProviderAccessToken(): AccessToken;

    public function getCache(): CacheInterface;
}
