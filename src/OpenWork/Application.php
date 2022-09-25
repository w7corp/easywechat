<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use function array_merge;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;
use EasyWeChat\OpenWork\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenWork\Contracts\SuiteTicket as SuiteTicketInterface;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\OpenWeWork;

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

    protected ?SuiteEncryptor $suiteEncryptor = null;

    protected ?SuiteTicketInterface $suiteTicket = null;

    protected ?AccessTokenInterface $accessToken = null;

    protected ?AccessTokenInterface $suiteAccessToken = null;

    public function getAccount(): AccountInterface
    {
        if (! $this->account) {
            $this->account = new Account(
                corpId: (string) $this->config->get('corp_id'), /** @phpstan-ignore-line */
                providerSecret: (string) $this->config->get('provider_secret'), /** @phpstan-ignore-line */
                suiteId: (string) $this->config->get('suite_id'), /** @phpstan-ignore-line */
                suiteSecret: (string) $this->config->get('suite_secret'), /** @phpstan-ignore-line */
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

    public function getSuiteEncryptor(): SuiteEncryptor
    {
        if (! $this->suiteEncryptor) {
            $this->suiteEncryptor = new SuiteEncryptor(
                suiteId: $this->getAccount()->getSuiteId(),
                token: $this->getAccount()->getToken(),
                aesKey: $this->getAccount()->getAesKey(),
            );
        }

        return $this->suiteEncryptor;
    }

    public function setSuiteEncryptor(SuiteEncryptor $encryptor): static
    {
        $this->suiteEncryptor = $encryptor;

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Throwable
     */
    public function getServer(): Server|ServerInterface
    {
        if (! $this->server) {
            $this->server = new Server(
                encryptor: $this->getSuiteEncryptor(),
                providerEncryptor: $this->getEncryptor(),
                request: $this->getRequest(),
            );

            $this->server->withDefaultSuiteTicketHandler(function (Message $message, \Closure $next): mixed {
                if ($message->SuiteId === $this->getAccount()->getSuiteId()) {
                    $this->getSuiteTicket()->setTicket($message->SuiteTicket);
                }

                return $next($message);
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
        if (! $this->accessToken) {
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
        if (! $this->suiteAccessToken) {
            $this->suiteAccessToken = new SuiteAccessToken(
                suiteId: $this->getAccount()->getSuiteId(),
                suiteSecret: $this->getAccount()->getSuiteSecret(),
                suiteTicket: $this->getSuiteTicket(),
                cache: $this->getCache(),
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

    public function getSuiteTicket(): SuiteTicketInterface
    {
        if (! $this->suiteTicket) {
            $this->suiteTicket = new SuiteTicket(
                suiteId: $this->getAccount()->getSuiteId(),
                cache: $this->getCache(),
            );
        }

        return $this->suiteTicket;
    }

    public function setSuiteTicket(SuiteTicketInterface $suiteTicket): SuiteTicketInterface
    {
        $this->suiteTicket = $suiteTicket;

        return $this->suiteTicket;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getAuthorization(
        string $corpId,
        string $permanentCode,
        ?AccessTokenInterface $suiteAccessToken = null
    ): Authorization {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();

        $response = $this->getHttpClient()->request('POST', 'cgi-bin/service/get_auth_info', [
            'query' => [
                'suite_access_token' => $suiteAccessToken->getToken(),
            ],
            'json' => [
                'auth_corpid' => $corpId,
                'permanent_code' => $permanentCode,
            ],
        ])->toArray(false);

        if (empty($response['auth_corp_info'])) {
            throw new HttpException('Failed to get auth_corp_info: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return new Authorization($response);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getAuthorizerAccessToken(
        string $corpId,
        string $permanentCode,
        ?AccessTokenInterface $suiteAccessToken = null
    ): AuthorizerAccessToken {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();
        $response = $this->getHttpClient()->request('POST', 'cgi-bin/service/get_corp_token', [
            'query' => [
                'suite_access_token' => $suiteAccessToken->getToken(),
            ],
            'json' => [
                'auth_corpid' => $corpId,
                'permanent_code' => $permanentCode,
            ],
        ])->toArray(false);

        if (empty($response['access_token'])) {
            throw new HttpException('Failed to get access_token: '.json_encode($response, JSON_UNESCAPED_UNICODE));
        }

        return new AuthorizerAccessToken($corpId, accessToken: $response['access_token']);
    }

    public function createClient(): AccessTokenAwareClient
    {
        return (new AccessTokenAwareClient(
            client: $this->getHttpClient(),
            accessToken: $this->getProviderAccessToken(),
            failureJudge: fn (Response $response) => (bool) ($response->toArray()['errcode'] ?? 0),
            throw: (bool) $this->config->get('http.throw', true),
        ))->setPresets($this->config->all());
    }

    public function getOAuth(
        string $suiteId,
        ?AccessTokenInterface $suiteAccessToken = null
    ): SocialiteProviderInterface {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();

        return (new OpenWeWork([
            'client_id' => $suiteId,
            'redirect_url' => $this->config->get('oauth.redirect_url'),
        ]))->withSuiteTicket($this->getSuiteTicket()->getTicket())
            ->withSuiteAccessToken($suiteAccessToken->getToken())
            ->scopes((array) $this->config->get('oauth.scopes', ['snsapi_base']));
    }

    public function getCorpOAuth(
        string $corpId,
        ?AccessTokenInterface $suiteAccessToken = null
    ): SocialiteProviderInterface {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();

        return (new OpenWeWork([
            'client_id' => $corpId,
            'redirect_url' => $this->config->get('oauth.redirect_url'),
        ]))->withSuiteTicket($this->getSuiteTicket()->getTicket())
            ->withSuiteAccessToken($suiteAccessToken->getToken())
            ->scopes((array) $this->config->get('oauth.scopes', ['snsapi_base']));
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
}
