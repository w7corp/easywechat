<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\JsApiTicket as JsApiTicketInterface;
use EasyWeChat\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use EasyWeChat\Kernel\Contracts\RefreshableJsApiTicket as RefreshableJsApiTicketInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Kernel\Traits\InteractsWithAppIdAccount;
use EasyWeChat\Kernel\Traits\InteractsWithWeChatApiClient;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\ResetsResolvedDependencies;
use EasyWeChat\Kernel\Traits\SynchronizesServerRequest;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationInterface;
use JetBrains\PhpStorm\Pure;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\WeChat;
use Psr\Log\LoggerAwareTrait;

use function array_merge;
use function call_user_func;
use function sprintf;

class Application implements ApplicationInterface
{
    use InteractsWithAppIdAccount;
    use InteractsWithWeChatApiClient;
    use InteractWithCache;
    use InteractWithClient;
    use InteractWithConfig;
    use InteractWithHttpClient;
    use LoggerAwareTrait;
    use ResetsResolvedDependencies;
    use SynchronizesServerRequest;

    protected ?Encryptor $encryptor = null;

    protected bool $usesCustomEncryptor = false;

    protected ?ServerInterface $server = null;

    protected bool $usesCustomServer = false;

    protected ?AccountInterface $account = null;

    protected bool $usesCustomAccount = false;

    protected AccessTokenInterface|RefreshableAccessTokenInterface|null $accessToken = null;

    protected bool $usesCustomAccessToken = false;

    protected ?JsApiTicketInterface $ticket = null;

    protected bool $usesCustomTicket = false;

    protected ?\Closure $oauthFactory = null;

    public function getAccount(): AccountInterface
    {
        if (! $this->account) {
            $this->account = $this->createAppIdAccount(Account::class);
            $this->usesCustomAccount = false;
        }

        return $this->account;
    }

    public function setAccount(AccountInterface $account): static
    {
        $this->account = $account;
        $this->usesCustomAccount = true;
        $this->refreshDerivedDependenciesAfterAccountUpdated();

        return $this;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getEncryptor(): Encryptor
    {
        if (! $this->encryptor) {
            $this->encryptor = $this->createAppIdEncryptor(
                appId: $this->getAccount()->getAppId(),
                token: $this->getAccount()->getToken(),
                aesKey: $this->getAccount()->getAesKey(),
            );
            $this->usesCustomEncryptor = false;
        }

        return $this->encryptor;
    }

    public function setEncryptor(Encryptor $encryptor): static
    {
        $this->encryptor = $encryptor;
        $this->usesCustomEncryptor = true;

        return $this;
    }

    public function getServer(): Server|ServerInterface
    {
        if (! $this->server) {
            $this->server = $this->createAppIdServer(
                serverClass: Server::class,
                encryptor: $this->getAccount()->getAesKey() ? $this->getEncryptor() : null,
            );
            $this->usesCustomServer = false;
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;
        $this->usesCustomServer = true;

        return $this;
    }

    public function getAccessToken(): AccessTokenInterface|RefreshableAccessTokenInterface
    {
        if (! $this->accessToken) {
            $this->accessToken = new AccessToken(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getHttpClient(),
                stable: $this->getBoolConfig('use_stable_access_token'),
            );
            $this->usesCustomAccessToken = false;
        }

        return $this->accessToken;
    }

    public function setAccessToken(AccessTokenInterface|RefreshableAccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;
        $this->usesCustomAccessToken = true;
        $this->resetClient();

        return $this;
    }

    public function setOAuthFactory(callable $factory): static
    {
        $this->oauthFactory = fn (Application $app): WeChat => $factory($app);

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getOAuth(): SocialiteProviderInterface
    {
        if (! $this->oauthFactory) {
            $scopes = $this->getStringListConfig('oauth.scopes', ['snsapi_userinfo']);

            $this->oauthFactory = fn (self $app): SocialiteProviderInterface => (new WeChat(
                [
                    'client_id' => $this->getAccount()->getAppId(),
                    'client_secret' => $this->getAccount()->getSecret(),
                    'redirect_url' => $this->config->get('oauth.redirect_url'),
                ]
            ))->scopes($scopes);
        }

        $provider = call_user_func($this->oauthFactory, $this);

        if (! $provider instanceof SocialiteProviderInterface) {
            throw new InvalidArgumentException(sprintf(
                'The factory must return a %s instance.',
                SocialiteProviderInterface::class
            ));
        }

        return $provider;
    }

    public function getTicket(): JsApiTicketInterface|RefreshableJsApiTicketInterface
    {
        if (! $this->ticket) {
            $this->ticket = new JsApiTicket(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getClient(),
                stable: $this->getBoolConfig('use_stable_access_token'),
            );
            $this->usesCustomTicket = false;
        }

        return $this->ticket;
    }

    public function setTicket(JsApiTicketInterface|RefreshableJsApiTicketInterface $ticket): static
    {
        $this->ticket = $ticket;
        $this->usesCustomTicket = true;

        return $this;
    }

    #[Pure]
    public function getUtils(): Utils
    {
        return new Utils($this);
    }

    public function createClient(): AccessTokenAwareClient
    {
        return $this->createWeChatApiClient(
            accessToken: $this->getAccessToken(),
            failureJudge: fn (Response $response): bool => (bool) ($response->toArray()['errcode'] ?? 0),
        );
    }

    /**
     * @return array<string,mixed>
     */
    protected function getHttpClientDefaultOptions(): array
    {
        return array_merge(
            ['base_uri' => 'https://api.weixin.qq.com/'],
            (array) $this->config->get('http', [])
        );
    }

    protected function afterHttpClientUpdated(): void
    {
        $this->resetClient();
    }

    protected function afterConfigUpdated(): void
    {
        $this->resetHttpClient();
        $this->resetClient();

        if (! $this->usesCustomAccount) {
            $this->account = null;
            $this->resetResolvedDependencies([
                [$this->usesCustomEncryptor, fn (): mixed => $this->encryptor = null],
                [$this->usesCustomServer, fn (): mixed => $this->server = null],
            ]);
        }

        $this->resetResolvedDependencies([
            [$this->usesCustomAccessToken, fn (): mixed => $this->accessToken = null],
            [$this->usesCustomTicket, fn (): mixed => $this->ticket = null],
        ]);
    }

    protected function refreshDerivedDependenciesAfterAccountUpdated(): void
    {
        $this->resetResolvedDependencies([
            [$this->usesCustomEncryptor, fn (): mixed => $this->encryptor = null],
            [$this->usesCustomServer, fn (): mixed => $this->server = null],
            [$this->usesCustomAccessToken, fn (): mixed => $this->accessToken = null],
            [$this->usesCustomTicket, fn (): mixed => $this->ticket = null],
        ]);

        $this->resetClient();
    }
}
