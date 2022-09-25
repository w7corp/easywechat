<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use function array_merge;
use Closure;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\OfficialAccount\Config as OfficialAccountConfig;
use EasyWeChat\OpenPlatform\Contracts\Account as AccountInterface;
use EasyWeChat\OpenPlatform\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use function is_string;
use function md5;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\WeChat;
use Psr\SimpleCache\InvalidArgumentException;
use function sprintf;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Application implements ApplicationInterface
{
    use InteractWithCache;
    use InteractWithConfig;
    use InteractWithClient;
    use InteractWithHttpClient;
    use InteractWithServerRequest;

    protected ?Encryptor $encryptor = null;

    protected ?ServerInterface $server = null;

    protected ?AccountInterface $account = null;

    protected ?AccessTokenInterface $componentAccessToken = null;

    protected ?VerifyTicketInterface $verifyTicket = null;

    public function getAccount(): AccountInterface
    {
        if (! $this->account) {
            $this->account = new Account(
                appId: (string) $this->config->get('app_id'), /** @phpstan-ignore-line */
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

    public function getVerifyTicket(): VerifyTicketInterface
    {
        if (! $this->verifyTicket) {
            $this->verifyTicket = new VerifyTicket(
                appId: $this->getAccount()->getAppId(),
                cache: $this->getCache(),
            );
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
        if (! $this->encryptor) {
            $this->encryptor = new Encryptor(
                appId: $this->getAccount()->getAppId(),
                token: $this->getAccount()->getToken(),
                aesKey: $this->getAccount()->getAesKey(),
                receiveId: $this->getAccount()->getAppId(),
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
    public function getServer(): Server|ServerInterface
    {
        if (! $this->server) {
            $this->server = new Server(
                encryptor: $this->getEncryptor(),
                request: $this->getRequest()
            );
        }

        if ($this->server instanceof Server) {
            $this->server->withDefaultVerifyTicketHandler(
                function (Message $message, Closure $next): mixed {
                    $ticket = $this->getVerifyTicket();
                    if (\is_callable([$ticket, 'setTicket'])) {
                        $ticket->setTicket($message->ComponentVerifyTicket);
                    }

                    return $next($message);
                }
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
        return $this->getComponentAccessToken();
    }

    public function getComponentAccessToken(): AccessTokenInterface
    {
        if (! $this->componentAccessToken) {
            $this->componentAccessToken = new ComponentAccessToken(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
                verifyTicket: $this->getVerifyTicket(),
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

    /**
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function getAuthorization(string $authorizationCode): Authorization
    {
        $response = $this->getClient()->request(
            'POST',
            'cgi-bin/component/api_query_auth',
            [
                'json' => [
                    'component_appid' => $this->getAccount()->getAppId(),
                    'authorization_code' => $authorizationCode,
                ],
            ]
        )->toArray(false);

        if (empty($response['authorization_info'])) {
            throw new HttpException('Failed to get authorization_info: '.json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            ));
        }

        return new Authorization($response);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function refreshAuthorizerToken(string $authorizerAppId, string $authorizerRefreshToken): array
    {
        $response = $this->getClient()->request(
            'POST',
            'cgi-bin/component/api_authorizer_token',
            [
                'json' => [
                    'component_appid' => $this->getAccount()->getAppId(),
                    'authorizer_appid' => $authorizerAppId,
                    'authorizer_refresh_token' => $authorizerRefreshToken,
                ],
            ]
        )->toArray(false);

        if (empty($response['authorizer_access_token'])) {
            throw new HttpException('Failed to get authorizer_access_token: '.json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            ));
        }

        return $response;
    }

    /**
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws BadResponseException
     */
    public function createPreAuthorizationCode(): array
    {
        $response = $this->getClient()->request(
            'POST',
            'cgi-bin/component/api_create_preauthcode',
            [
                'json' => [
                    'component_appid' => $this->getAccount()->getAppId(),
                ],
            ]
        )->toArray(false);

        if (empty($response['pre_auth_code'])) {
            throw new HttpException('Failed to get authorizer_access_token: '.json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            ));
        }

        return $response;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function createPreAuthorizationUrl(string $callbackUrl, array|string $optional = []): string
    {
        // 兼容旧版 API 设计
        if (is_string($optional)) {
            $optional = [
                'pre_auth_code' => $optional,
            ];
        } else {
            $optional['pre_auth_code'] = Arr::get($this->createPreAuthorizationCode(), 'pre_auth_code');
        }

        $queries = array_merge(
            $optional,
            [
                'component_appid' => $this->getAccount()->getAppId(),
                'redirect_uri' => $callbackUrl,
            ]
        );

        return 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?'.http_build_query($queries);
    }

    /**
     * @throws \Overtrue\Socialite\Exceptions\InvalidArgumentException
     */
    public function getOAuth(): SocialiteProviderInterface
    {
        return (new WeChat(
            [
                'client_id' => $this->getAccount()->getAppId(),
                'client_secret' => $this->getAccount()->getSecret(),
                'redirect_url' => $this->config->get('oauth.redirect_url'),
            ]
        ))->scopes((array) $this->config->get('oauth.scopes', ['snsapi_userinfo']));
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws BadResponseException
     */
    public function getOfficialAccountWithRefreshToken(
        string $appId,
        string $refreshToken,
        array $config = []
    ): OfficialAccountApplication {
        return $this->getOfficialAccountWithAccessToken(
            $appId,
            $this->getAuthorizerAccessToken($appId, $refreshToken),
            $config
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getOfficialAccountWithAccessToken(
        string $appId,
        string $accessToken,
        array $config = []
    ): OfficialAccountApplication {
        return $this->getOfficialAccount(new AuthorizerAccessToken($appId, $accessToken), $config);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getOfficialAccount(
        AuthorizerAccessToken $authorizerAccessToken,
        array $config = []
    ): OfficialAccountApplication {
        $config = new OfficialAccountConfig(
            array_merge(
                [
                    'app_id' => $authorizerAccessToken->getAppId(),
                    'token' => $this->config->get('token'),
                    'aes_key' => $this->config->get('aes_key'),
                    'logging' => $this->config->get('logging'),
                    'http' => $this->config->get('http'),
                ],
                $config
            )
        );

        $app = new OfficialAccountApplication($config);

        $app->setAccessToken($authorizerAccessToken);
        $app->setEncryptor($this->getEncryptor());
        $app->setOAuthFactory($this->createAuthorizerOAuthFactory($authorizerAccessToken->getAppId(), $config));

        return $app;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws HttpException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws BadResponseException
     */
    public function getMiniAppWithRefreshToken(
        string $appId,
        string $refreshToken,
        array $config = []
    ): MiniAppApplication {
        return $this->getMiniAppWithAccessToken(
            $appId,
            $this->getAuthorizerAccessToken($appId, $refreshToken),
            $config
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getMiniAppWithAccessToken(
        string $appId,
        string $accessToken,
        array $config = []
    ): MiniAppApplication {
        return $this->getMiniApp(new AuthorizerAccessToken($appId, $accessToken), $config);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getMiniApp(AuthorizerAccessToken $authorizerAccessToken, array $config = []): MiniAppApplication
    {
        $app = new MiniAppApplication(
            array_merge(
                [
                    'app_id' => $authorizerAccessToken->getAppId(),
                    'token' => $this->config->get('token'),
                    'aes_key' => $this->config->get('aes_key'),
                    'logging' => $this->config->get('logging'),
                    'http' => $this->config->get('http'),
                ],
                $config
            )
        );

        $app->setAccessToken($authorizerAccessToken);
        $app->setEncryptor($this->getEncryptor());

        return $app;
    }

    protected function createAuthorizerOAuthFactory(string $authorizerAppId, OfficialAccountConfig $config): Closure
    {
        return fn () => (new WeChat(
            [
                'client_id' => $authorizerAppId,

                'component' => [
                    'component_app_id' => $this->getAccount()->getAppId(),
                    'component_access_token' => fn () => $this->getComponentAccessToken()->getToken(),
                ],

                'redirect_url' => $this->config->get('oauth.redirect_url'),
            ]
        ))->scopes((array) $config->get('oauth.scopes', ['snsapi_userinfo']));
    }

    public function createClient(): AccessTokenAwareClient
    {
        return (new AccessTokenAwareClient(
            client: $this->getHttpClient(),
            accessToken: $this->getComponentAccessToken(),
            failureJudge: fn (Response $response) => (bool) ($response->toArray()['errcode'] ?? 0),
            throw: (bool) $this->config->get('http.throw', true),
        ))->setPresets($this->config->all());
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     * @throws HttpException
     * @throws ServerExceptionInterface
     * @throws BadResponseException
     */
    public function getAuthorizerAccessToken(string $appId, string $refreshToken): string
    {
        $cacheKey = sprintf('open-platform.authorizer_access_token.%s.%s', $appId, md5($refreshToken));

        /** @phpstan-ignore-next-line */
        $authorizerAccessToken = (string) $this->getCache()->get($cacheKey);

        if (! $authorizerAccessToken) {
            $response = $this->refreshAuthorizerToken($appId, $refreshToken);
            $authorizerAccessToken = (string) $response['authorizer_access_token'];
            $this->getCache()->set($cacheKey, $authorizerAccessToken, intval($response['expires_in'] ?? 7200) - 500);
        }

        return $authorizerAccessToken;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getHttpClientDefaultOptions(): array
    {
        return array_merge(
            ['base_uri' => 'https://api.weixin.qq.com/'],
            (array) $this->config->get('http', [])
        );
    }
}
