<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Contracts;

use EasyWeChat\Kernel\Contracts\AccessToken;
use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient;
use EasyWeChat\Kernel\Contracts\Server;
use EasyWeChat\Kernel\UriBuilder;
use EasyWeChat\Kernel\Contracts\Config;
use EasyWeChat\Kernel\Encryptor;
use Overtrue\Socialite\Providers\WeChat;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;

interface Application
{
    public function getAccount(): Account;

    public function getEncryptor(): Encryptor;

    public function getServer(): Server;

    public function getRequest(): ServerRequestInterface;

    public function getClient(): UriBuilder;

    public function getHttpClient(): AccessTokenAwareHttpClient;

    public function getConfig(): Config;

    public function getAccessToken(): AccessToken;

    public function getCache(): CacheInterface;

    public function getOAuth(): WeChat;

    public function setOAuthFactory(callable $factory): static;
}
