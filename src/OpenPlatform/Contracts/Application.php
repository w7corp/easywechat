<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Contracts;

use EasyWeChat\Kernel\Contracts\AccessToken;
use EasyWeChat\Kernel\Contracts\Config;
use EasyWeChat\Kernel\Contracts\Server;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\OpenPlatform\AuthorizerAccessToken;
use Overtrue\Socialite\Contracts\ProviderInterface;
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

    public function getComponentAccessToken(): AccessToken;

    public function getCache(): CacheInterface;

    public function getOAuth(): ProviderInterface;

    /**
     * @param  array<string, mixed>  $config
     */
    public function getMiniApp(AuthorizerAccessToken $authorizerAccessToken, array $config): MiniAppApplication;

    /**
     * @param  array<string, mixed>  $config
     */
    public function getOfficialAccount(
        AuthorizerAccessToken $authorizerAccessToken,
        array $config
    ): OfficialAccountApplication;
}
