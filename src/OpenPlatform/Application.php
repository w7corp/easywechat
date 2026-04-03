<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use Closure;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Support\Arr;
use EasyWeChat\Kernel\Traits\InteractsWithAppIdAccount;
use EasyWeChat\Kernel\Traits\InteractsWithWeChatApiClient;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\SynchronizesServerRequest;
use EasyWeChat\MiniApp\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Application as OfficialAccountApplication;
use EasyWeChat\OfficialAccount\Config as OfficialAccountConfig;
use EasyWeChat\OpenPlatform\Contracts\Account as AccountInterface;
use EasyWeChat\OpenPlatform\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\WeChat;
use Psr\Log\LoggerAwareTrait;

use function array_merge;
use function is_string;
use function md5;
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
    use SynchronizesServerRequest;

    protected ?Encryptor $encryptor = null;

    protected bool $usesCustomEncryptor = false;

    protected ?ServerInterface $server = null;

    protected bool $usesCustomServer = false;

    protected ?AccountInterface $account = null;

    protected bool $usesCustomAccount = false;

    protected ?AccessTokenInterface $componentAccessToken = null;

    protected bool $usesCustomComponentAccessToken = false;

    protected ?VerifyTicketInterface $verifyTicket = null;

    protected bool $usesCustomVerifyTicket = false;

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

    public function getVerifyTicket(): VerifyTicketInterface
    {
        if (! $this->verifyTicket) {
            $this->verifyTicket = new VerifyTicket(
                appId: $this->getAccount()->getAppId(),
                cache: $this->getCache(),
            );
            $this->usesCustomVerifyTicket = false;
        }

        return $this->verifyTicket;
    }

    public function setVerifyTicket(VerifyTicketInterface $verifyTicket): static
    {
        $this->verifyTicket = $verifyTicket;
        $this->usesCustomVerifyTicket = true;

        return $this;
    }

    public function getEncryptor(): Encryptor
    {
        if (! $this->encryptor) {
            $this->encryptor = $this->createAppIdEncryptor(
                appId: $this->getAccount()->getAppId(),
                token: $this->getAccount()->getToken(),
                aesKey: $this->getAccount()->getAesKey(),
                requireCredentials: false,
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
                encryptor: $this->getEncryptor(),
            );
            $this->usesCustomServer = false;

            $this->server->withDefaultVerifyTicketHandler(
                function (Message $message, Closure $next): mixed {
                    $ticket = $this->getVerifyTicket();
                    $ticket->setTicket($message->ComponentVerifyTicket);

                    return $next($message);
                }
            );
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
            $this->usesCustomComponentAccessToken = false;
        }

        return $this->componentAccessToken;
    }

    public function setComponentAccessToken(AccessTokenInterface $componentAccessToken): static
    {
        $this->componentAccessToken = $componentAccessToken;
        $this->usesCustomComponentAccessToken = true;
        $this->resetClient();

        return $this;
    }

    /**
     * @throws HttpException
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
     * @throws HttpException
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

    public function getOfficialAccountWithAccessToken(
        string $appId,
        string $accessToken,
        array $config = []
    ): OfficialAccountApplication {
        return $this->getOfficialAccount(new AuthorizerAccessToken($appId, $accessToken), $config);
    }

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
                    'http' => $this->config->get('http', []),
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

    public function getMiniAppWithAccessToken(
        string $appId,
        string $accessToken,
        array $config = []
    ): MiniAppApplication {
        return $this->getMiniApp(new AuthorizerAccessToken($appId, $accessToken), $config);
    }

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
        return $this->createErrcodeAwareClient($this->getComponentAccessToken());
    }

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

            if (! $this->usesCustomEncryptor) {
                $this->encryptor = null;
            }

            if (! $this->usesCustomServer) {
                $this->server = null;
            }

            if (! $this->usesCustomVerifyTicket) {
                $this->verifyTicket = null;
            }
        }

        if (! $this->usesCustomComponentAccessToken) {
            $this->componentAccessToken = null;
        }
    }

    protected function refreshDerivedDependenciesAfterAccountUpdated(): void
    {
        if (! $this->usesCustomEncryptor) {
            $this->encryptor = null;
        }

        if (! $this->usesCustomServer) {
            $this->server = null;
        }

        if (! $this->usesCustomComponentAccessToken) {
            $this->componentAccessToken = null;
        }

        if (! $this->usesCustomVerifyTicket) {
            $this->verifyTicket = null;
        }

        $this->resetClient();
    }
}
