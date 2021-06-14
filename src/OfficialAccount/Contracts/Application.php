<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Contracts;

use EasyWeChat\Kernel\UriBuilder;
use EasyWeChat\Kernel\Contracts\Config;
use EasyWeChat\Kernel\Encryptor;
use Psr\SimpleCache\CacheInterface;

interface Application
{
    public function getAccount(): Account;

    public function getEncryptor(): Encryptor;

    public function getServer(): Server;

    public function getRequest(): Request;

    public function getClient(): UriBuilder;

    public function getHttpClient(): HttpClient;

    public function getConfig(): Config;

    public function getAccessToken(): AccessToken;

    public function getCache(): CacheInterface;
}
