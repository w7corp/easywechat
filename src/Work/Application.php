<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Work\Contracts\Account as AccountInterface;
use EasyWeChat\Work\Contracts\Application as ApplicationInterface;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\WeWork;
use Psr\Log\LoggerAwareTrait;

use function array_merge;

class Application implements ApplicationInterface
{
    use InteractWithCache;
    use InteractWithClient;
    use InteractWithConfig;
    use InteractWithHttpClient;
    use InteractWithServerRequest;
    use LoggerAwareTrait;

    protected ?Encryptor $encryptor = null;

    protected ?ServerInterface $server = null;

    protected ?AccountInterface $account = null;

    protected ?JsApiTicket $ticket = null;

    protected ?AccessTokenInterface $accessToken = null;

    protected ?KfMessage $kfMessage = null;

    protected ?KfAccount $kfAccount = null;

    protected ?KfServicer $kfServicer = null;

    public function getAccount(): AccountInterface
    {
        if (! $this->account) {
            $this->account = new Account(
                corpId: (string) $this->config->get('corp_id'), /** @phpstan-ignore-line */
                secret: (string) $this->config->get('secret'), /** @phpstan-ignore-line */
                token: (string) $this->config->get('token'), /** @phpstan-ignore-line */
                aesKey: (string) $this->config->get('aes_key'),/** @phpstan-ignore-line */
            );
        }

        return $this->account;
    }

    public function setAccount(AccountInterface $account): static
    {
        $this->account = $account;

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
        }

        return $this->encryptor;
    }

    public function setEncryptor(Encryptor $encryptor): static
    {
        $this->encryptor = $encryptor;

        return $this;
    }

    public function getServer(string $messageType = 'xml'): Server|ServerInterface
    {
        if (! $this->server) {
            $this->server = new Server(
                encryptor: $this->getEncryptor(),
                request: $this->getRequest(),
                messageType: $messageType,
            );
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;

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
        }

        return $this->accessToken;
    }

    public function setAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getUtils(): Utils
    {
        return new Utils($this);
    }

    public function createClient(): AccessTokenAwareClient
    {
        return (new AccessTokenAwareClient(
            client: $this->getHttpClient(),
            accessToken: $this->getAccessToken(),
            failureJudge: fn (Response $response) => (bool) ($response->toArray()['errcode'] ?? 0),
            throw: (bool) $this->config->get('http.throw', true),
        ))->setPresets($this->config->all());
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

        $provider->withApiAccessToken($this->getAccessToken()->getToken());
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
        }

        return $this->ticket;
    }

    public function setTicket(JsApiTicket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getKfMessage(): KfMessage
    {
        if (! $this->kfMessage) {
            $this->kfMessage = new KfMessage($this->getClient());
        }

        return $this->kfMessage;
    }

    public function setKfMessage(KfMessage $kfMessage): static
    {
        $this->kfMessage = $kfMessage;

        return $this;
    }

    public function getKfAccount(): KfAccount
    {
        if (! $this->kfAccount) {
            $this->kfAccount = new KfAccount($this->getClient());
        }

        return $this->kfAccount;
    }

    public function setKfAccount(KfAccount $kfAccount): static
    {
        $this->kfAccount = $kfAccount;

        return $this;
    }

    public function getKfServicer(): KfServicer
    {
        if (! $this->kfServicer) {
            $this->kfServicer = new KfServicer($this->getClient());
        }

        return $this->kfServicer;
    }

    public function setKfServicer(KfServicer $kfServicer): static
    {
        $this->kfServicer = $kfServicer;

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

    /**
     * Handle dynamic property access for customer service (kf) modules.
     *
     * @param  string  $property
     * @return KfMessage|KfAccount|KfServicer
     */
    public function __get(string $property): mixed
    {
        $propertyMap = [
            'kf_message' => 'getKfMessage',
            'kf_account' => 'getKfAccount',
            'kf_servicer' => 'getKfServicer',
        ];

        if (isset($propertyMap[$property])) {
            return $this->{$propertyMap[$property]}();
        }

        throw new \BadMethodCallException(sprintf('Property %s does not exist.', $property));
    }
}
