<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Traits\InteractsWithWeChatApiClient;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\ResetsResolvedDependencies;
use EasyWeChat\Kernel\Traits\SynchronizesServerRequest;
use EasyWeChat\Work\Contracts\Account as AccountInterface;
use EasyWeChat\Work\Contracts\Application as ApplicationInterface;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\WeWork;
use Psr\Log\LoggerAwareTrait;

use function array_merge;

class Application implements ApplicationInterface
{
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

    protected ?string $serverMessageType = null;

    protected bool $usesCustomServer = false;

    protected ?AccountInterface $account = null;

    protected bool $usesCustomAccount = false;

    protected ?JsApiTicket $ticket = null;

    protected bool $usesCustomTicket = false;

    protected ?AccessTokenInterface $accessToken = null;

    protected bool $usesCustomAccessToken = false;

    public function getAccount(): AccountInterface
    {
        if (! $this->account) {
            $this->account = new Account(
                corpId: (string) $this->config->get('corp_id'), /** @phpstan-ignore-line */
                secret: (string) $this->config->get('secret'), /** @phpstan-ignore-line */
                token: (string) $this->config->get('token'), /** @phpstan-ignore-line */
                aesKey: (string) $this->config->get('aes_key'),/** @phpstan-ignore-line */
            );
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

    public function getEncryptor(): Encryptor
    {
        if (! $this->encryptor) {
            $this->encryptor = new Encryptor(
                corpId: $this->getAccount()->getCorpId(),
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

    public function getServer(string $messageType = 'xml'): Server|ServerInterface
    {
        if ($this->usesCustomServer && $this->server) {
            return $this->server;
        }

        if (! $this->server || $this->serverMessageType !== $messageType) {
            $this->server = new Server(
                encryptor: $this->getEncryptor(),
                request: $this->getRequest(),
                messageType: $messageType,
            );
            $this->serverMessageType = $messageType;
            $this->usesCustomServer = false;
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;
        $this->usesCustomServer = true;
        $this->serverMessageType = null;

        return $this;
    }

    public function getAccessToken(): AccessTokenInterface
    {
        if (! $this->accessToken) {
            $this->accessToken = new AccessToken(
                corpId: $this->getAccount()->getCorpId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getHttpClient(),
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

    public function getUtils(): Utils
    {
        return new Utils($this);
    }

    public function createClient(): AccessTokenAwareClient
    {
        return $this->createErrcodeAwareClient($this->getAccessToken());
    }

    public function getOAuth(): SocialiteProviderInterface
    {
        $provider = new WeWork(
            [
                'client_id' => $this->getAccount()->getCorpId(),
                'client_secret' => $this->getAccount()->getSecret(),
                'redirect_url' => $this->config->get('oauth.redirect_url'),
            ]
        );

        $provider->scopes((array) $this->config->get('oauth.scopes', ['snsapi_base']));

        if ($this->config->has('agent_id') && \is_numeric($this->config->get('agent_id'))) {
            $provider->withAgentId((int) $this->config->get('agent_id'));
        }

        return $provider;
    }

    public function getTicket(): JsApiTicket
    {
        if (! $this->ticket) {
            $this->ticket = new JsApiTicket(
                corpId: $this->getAccount()->getCorpId(),
                cache: $this->getCache(),
                httpClient: $this->getClient(),
            );
            $this->usesCustomTicket = false;
        }

        return $this->ticket;
    }

    public function setTicket(JsApiTicket $ticket): static
    {
        $this->ticket = $ticket;
        $this->usesCustomTicket = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getHttpClientDefaultOptions(): array
    {
        return array_merge(
            ['base_uri' => 'https://qyapi.weixin.qq.com/'],
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
                [$this->usesCustomServer, function (): void {
                    $this->server = null;
                    $this->serverMessageType = null;
                }],
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
            [$this->usesCustomServer, function (): void {
                $this->server = null;
                $this->serverMessageType = null;
            }],
            [$this->usesCustomAccessToken, fn (): mixed => $this->accessToken = null],
            [$this->usesCustomTicket, fn (): mixed => $this->ticket = null],
        ]);

        $this->resetClient();
    }
}
