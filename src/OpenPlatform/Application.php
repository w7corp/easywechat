<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Kernel\UriBuilder;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\OpenPlatform\Contracts\Account as AccountInterface;
use EasyWeChat\OpenPlatform\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenPlatform\Contracts\HttpClient as HttpClientInterface;
use EasyWeChat\OpenPlatform\Contracts\Server as ServerInterface;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;

class Application implements ApplicationInterface
{
    use InteractWithConfig;
    use InteractWithCache;
    use InteractWithServerRequest;

    protected ?UriBuilder $client = null;
    protected ?Encryptor $encryptor = null;
    protected ?ServerInterface $server = null;
    protected ?AccountInterface $account = null;
    protected ?AccessTokenInterface $componentAccessToken = null;
    protected ?HttpClientInterface $httpClient = null;
    protected ?VerifyTicketInterface $verifyTicket = null;

    /**
     * @var array
     */
    public const DEFAULT_HTTP_OPTIONS = [
        'timeout' => 30.0,
        'base_uri' => 'https://api.weixin.qq.com/',
    ];

    public function getAccount(): AccountInterface
    {
        if (!$this->account) {
            $this->account = new Account(
                appId: $this->config->get('app_id'),
                secret: $this->config->get('secret'),
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

    public function getVerifyTicket(): VerifyTicketInterface
    {
        if (!$this->verifyTicket) {
            $this->verifyTicket = new VerifyTicket(appId: $this->getAccount()->getAppId());
        }

        return $this->verifyTicket;
    }

    public function setVerifyTicket(VerifyTicketInterface $verifyTicket): static
    {
        $this->verifyTicket = $verifyTicket;

        return $this;
    }

    public function getEncryptor(): Encryptor
    {
        if (!$this->encryptor) {
            $this->encryptor = new Encryptor(
                $this->getAccount()->getAppId(),
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
                encryptor: $this->getEncryptor()
            );
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;

        return $this;
    }

    public function getClient(): UriBuilder
    {
        if (!$this->client) {
            $this->client = new UriBuilder(client: $this->getHttpClient()->withAccessToken($this->getComponentAccessToken()));
        }

        return $this->client;
    }

    public function setClient(UriBuilder $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getHttpClient(): HttpClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = (new HttpClient())
                ->withOptions(\array_merge(self::DEFAULT_HTTP_OPTIONS, $this->config->get('http', [])));
        }

        return $this->httpClient;
    }

    public function setHttpClient(HttpClientInterface $httpClient): static
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    public function getComponentAccessToken(): AccessTokenInterface
    {
        if (!$this->componentAccessToken) {
            $this->componentAccessToken = new ComponentAccessToken(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                cache: $this->getCache(),
                httpClient: $this->getHttpClient(),
            );
        }

        return $this->componentAccessToken;
    }

    public function setComponentAccessToken(AccessTokenInterface $componentAccessToken): static
    {
        $this->componentAccessToken = $componentAccessToken;

        return $this;
    }

    public function getAuthorization(string $authorizationCode): Authorization
    {
        $response = $this->getHttpClient()->request(
            'POST',
            'cgi-bin/component/api_query_auth',
            [
                'json' => [
                    'component_appid' => $this->account->getAppId(),
                    'authorization_code' => $authorizationCode,
                ],
            ]
        )->toArray();

        if (empty($response['authorization_info'])) {
            throw new HttpException('Failed to get authorization_info.');
        }

        return new Authorization($response['authorization_info']);
    }

    public function refreshAuthorizerToken(string $authorizerAppId, string $authorizerRefreshToken)
    {
        $response = $this->getHttpClient()->request(
            'POST',
            'cgi-bin/component/api_authorizer_token',
            [
                'json' => [
                    'component_appid' => $this->account->getAppId(),
                    'authorizer_appid' => $authorizerAppId,
                    'authorizer_refresh_token' => $authorizerRefreshToken,
                ],
            ]
        )->toArray();

        if (empty($response['authorizer_access_token'])) {
            throw new HttpException('Failed to get authorizer_access_token.');
        }

        return $response;
    }
}
