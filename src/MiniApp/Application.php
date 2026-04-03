<?php

declare(strict_types=1);

namespace EasyWeChat\MiniApp;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Kernel\Traits\InteractsWithAppIdAccount;
use EasyWeChat\Kernel\Traits\InteractsWithWeChatApiClient;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\SynchronizesServerRequest;
use EasyWeChat\MiniApp\Contracts\Account as AccountInterface;
use EasyWeChat\MiniApp\Contracts\Application as ApplicationInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Log\LoggerAwareTrait;

use function array_merge;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Application implements ApplicationInterface
{
    use InteractsWithAppIdAccount;
    use InteractsWithWeChatApiClient;
    use InteractWithCache;
    use InteractWithClient;
    use InteractWithConfig;
    use InteractWithHttpClient;
    use LoggerAwareTrait;
    use SynchronizesServerRequest;

    protected ?Encryptor $encryptor = null;

    protected bool $usesCustomEncryptor = false;

    protected ?ServerInterface $server = null;

    protected bool $usesCustomServer = false;

    protected ?AccountInterface $account = null;

    protected ?AccessTokenInterface $accessToken = null;

    protected bool $usesCustomAccessToken = false;

    public function getAccount(): AccountInterface
    {
        if (! $this->account) {
            $this->account = $this->createAppIdAccount(Account::class);
        }

        return $this->account;
    }

    public function setAccount(AccountInterface $account): static
    {
        $this->account = $account;
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

    public function getAccessToken(): AccessTokenInterface
    {
        if (! $this->accessToken) {
            $this->accessToken = new AccessToken(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getHttpClient(),
                stable: $this->config->get('use_stable_access_token', false)
            );
            $this->usesCustomAccessToken = false;
        }

        return $this->accessToken;
    }

    public function setAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;
        $this->usesCustomAccessToken = true;
        $this->resetClient();

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
            failureJudge: function (Response $response): bool {
                $payload = $response->toArray();

                return (bool) (($payload['errcode'] ?? 0) || ($payload['error'] ?? null) !== null);
            },
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

    protected function refreshDerivedDependenciesAfterAccountUpdated(): void
    {
        if (! $this->usesCustomEncryptor) {
            $this->encryptor = null;
        }

        if (! $this->usesCustomServer) {
            $this->server = null;
        }

        if (! $this->usesCustomAccessToken) {
            $this->accessToken = null;
        }

        $this->resetClient();
    }
}
