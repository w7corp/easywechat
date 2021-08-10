<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Client;
use EasyWeChat\OpenPlatform\Authorization;
use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;
use EasyWeChat\OpenWork\Contracts\Application as ApplicationInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\OpenWork\Contracts\SuiteTicket as SuiteTicketInterface;
use Overtrue\Socialite\Providers\WeWork;

class Application implements ApplicationInterface
{
    use InteractWithCache;
    use InteractWithConfig;
    use InteractWithHttpClient;
    use InteractWithServerRequest;
    use InteractWithClient;

    protected ?ServerInterface $server = null;
    protected ?AccountInterface $account = null;
    protected ?Encryptor $encryptor = null;
    protected ?Encryptor $suiteEncryptor = null;
    protected ?SuiteTicketInterface $suiteTicket = null;
    protected ?AccessTokenInterface $accessToken = null;
    protected ?AccessTokenInterface $suiteAccessToken = null;

    public function getAccount(): AccountInterface
    {
        if (!$this->account) {
            $this->account = new Account(
                corpId: $this->config->get('corp_id'),
                providerSecret: $this->config->get('provider_secret'),
                suiteId: $this->config->get('suite_id'),
                suiteSecret: $this->config->get('suite_secret'),
                token: $this->config->get('token'),
                aesKey: $this->config->get('aes_key'),
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
        if (!$this->encryptor) {
            $this->encryptor = new Encryptor(
                $this->getAccount()->getCorpId(),
                $this->getAccount()->getToken(),
                $this->getAccount()->getAesKey(),
            );
        }

        return $this->encryptor;
    }

    public function setEncryptor(Encryptor $encryptor): static
    {
        $this->encryptor = $encryptor;

        return $this;
    }

    public function getSuiteEncryptor(): Encryptor
    {
        if (!$this->suiteEncryptor) {
            $this->suiteEncryptor = new Encryptor(
                $this->getAccount()->getSuiteId(),
                $this->getAccount()->getToken(),
                $this->getAccount()->getAesKey(),
            );
        }

        return $this->suiteEncryptor;
    }

    public function setSuiteEncryptor(Encryptor $encryptor): static
    {
        $this->suiteEncryptor = $encryptor;

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Throwable
     */
    public function getServer(): ServerInterface
    {
        if (!$this->server) {
            $this->server = new Server(
                account: $this->getAccount(),
                request: $this->getRequest(),
                encryptor: $this->getEncryptor(),
                suiteEncryptor: $this->getSuiteEncryptor(),
            );

            $this->server->withDefaultSuiteTicketHandler(function (\EasyWeChat\Kernel\Message $message, \Closure $next): mixed {
                $this->suiteTicket->setTicket($message['SuiteTicket']);
            });
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;

        return $this;
    }

    public function getProviderAccessToken(): AccessTokenInterface
    {
        if (!$this->accessToken) {
            $this->accessToken = new ProviderAccessToken(
                corpId: $this->getAccount()->getCorpId(),
                providerSecret: $this->getAccount()->getProviderSecret(),
                cache: $this->getCache(),
                httpClient: $this->getHttpClient(),
            );
        }

        return $this->accessToken;
    }

    public function setProviderAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getSuiteAccessToken(): AccessTokenInterface
    {
        if (!$this->suiteAccessToken) {
            $this->suiteAccessToken = new SuiteAccessToken(
                suiteId: $this->getAccount()->getSuiteId(),
                suiteSecret: $this->getAccount()->getSuiteSecret(),
                suiteTicket: $this->getSuiteTicket(),
                httpClient: $this->getHttpClient(),
            );
        }

        return $this->suiteAccessToken;
    }

    public function setSuiteAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->suiteAccessToken = $accessToken;

        return $this;
    }

    public function getSuiteTicket(): ?SuiteTicketInterface
    {
        if (!$this->suiteTicket) {
            $this->suiteTicket = new SuiteTicket(
                suiteId: $this->getAccount()->getSuiteId(),
                cache: $this->getCache(),
            );
        }

        return $this->suiteTicket;
    }

    public function setSuiteTicket(SuiteTicketInterface $suiteTicket): ?SuiteTicketInterface
    {
        $this->suiteTicket = $suiteTicket;

        return $this->suiteTicket;
    }

    public function getAuthorization(string $corpId, string $permanentCode, ?AccessTokenInterface $suiteAccessToken = null): Authorization
    {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();

        $response = $this->getHttpClient()->request(
            'POST',
            'cgi-bin/service/get_auth_info',
            [
                'query' => [
                    'suite_access_token' => $suiteAccessToken->getToken(),
                ],
                'json' => [
                    'auth_corpid' => $corpId,
                    'permanent_code' => $permanentCode,
                ],
            ]
        )->toArray();

        if (empty($response['auth_corp_info'])) {
            throw new HttpException('Failed to get auth_corp_info.');
        }

        return new Authorization($response);
    }

    public function getAuthorizerAccessToken(
        string $corpId,
        string $permanentCode,
        ?AccessTokenInterface $suiteAccessToken = null
    ): AuthorizerAccessToken {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();
        $response = $this->getHttpClient()->request(
            'POST',
            'cgi-bin/service/get_corp_token',
            [
                'query' => [
                    'suite_access_token' => $suiteAccessToken->getToken(),
                ],
                'json' => [
                    'auth_corpid' => $corpId,
                    'permanent_code' => $permanentCode,
                ],
            ]
        )->toArray();

        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get access_token.');
        }

        return new AuthorizerAccessToken($corpId, accessToken: $response['access_token']);
    }

    public function createClient(): Client
    {
        return new Client($this->getHttpClient(), '', $this->getProviderAccessToken());
    }

    public function getOAuth(string $suiteId, ?AccessTokenInterface $suiteAccessToken = null): WeWork
    {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();

        return (new WeWork(
            [
                'client_id' => $suiteId,
                'redirect_url' => $this->config->get('oauth.redirect_url'),
            ]
        ))->withApiAccessToken($suiteAccessToken->getToken())
            ->scopes($this->config->get('oauth.scopes', ['snsapi_base']));
    }

    public function getCorpOAuth(string $corpId, int $agentId, ?AccessTokenInterface $suiteAccessToken = null): WeWork
    {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();

        return (new WeWork(
            [
                'client_id' => $corpId,
                'redirect_url' => $this->config->get('oauth.redirect_url'),
            ]
        ))->setAgentId($agentId)
            ->withApiAccessToken($suiteAccessToken->getToken())
            ->scopes($this->config->get('oauth.scopes', ['snsapi_base']));
    }

    protected function getHttpClientDefaultOptions(): array
    {
        return \array_merge(
            ['base_uri' => 'https://qyapi.weixin.qq.com/',],
            (array)$this->config->get('http', [])
        );
    }
}
