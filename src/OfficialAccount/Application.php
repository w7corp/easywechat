<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use function array_merge;
use function call_user_func;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\RefreshableAccessToken as RefreshableAccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\AccessTokenExpiredRetryStrategy;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationInterface;
use JetBrains\PhpStorm\Pure;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\WeChat;
use Psr\Log\LoggerAwareTrait;
use function sprintf;
use function str_contains;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\RetryableHttpClient;

class Application implements ApplicationInterface
{
    use InteractWithConfig;
    use InteractWithCache;
    use InteractWithServerRequest;
    use InteractWithHttpClient;
    use InteractWithClient;
    use LoggerAwareTrait;

    protected ?Encryptor $encryptor = null;

    protected ?ServerInterface $server = null;

    protected ?AccountInterface $account = null;

    protected AccessTokenInterface|RefreshableAccessTokenInterface|null $accessToken = null;

    protected ?JsApiTicket $ticket = null;

    protected ?\Closure $oauthFactory = null;

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

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getEncryptor(): Encryptor
    {
        if (! $this->encryptor) {
            $token = $this->getAccount()->getToken();
            $aesKey = $this->getAccount()->getAesKey();

            if (empty($token) || empty($aesKey)) {
                throw new InvalidConfigException('token or aes_key cannot be empty.');
            }

            $this->encryptor = new Encryptor(
                appId: $this->getAccount()->getAppId(),
                token: $token,
                aesKey: $aesKey,
                receiveId: $this->getAccount()->getAppId()
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
     * @throws InvalidArgumentException
     * @throws \Throwable
     */
    public function getServer(): Server|ServerInterface
    {
        if (! $this->server) {
            $this->server = new Server(
                request: $this->getRequest(),
                encryptor: $this->getAccount()->getAesKey() ? $this->getEncryptor() : null
            );
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;

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
            );
        }

        return $this->accessToken;
    }

    public function setAccessToken(AccessTokenInterface|RefreshableAccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;

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
            $this->oauthFactory = fn (self $app): SocialiteProviderInterface => (new WeChat(
                [
                    'client_id' => $this->getAccount()->getAppId(),
                    'client_secret' => $this->getAccount()->getSecret(),
                    'redirect_url' => $this->config->get('oauth.redirect_url'),
                ]
            ))->scopes((array) $this->config->get('oauth.scopes', ['snsapi_userinfo']));
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

    public function getTicket(): JsApiTicket
    {
        if (! $this->ticket) {
            $this->ticket = new JsApiTicket(
                appId: $this->getAccount()->getAppId(),
                secret: $this->getAccount()->getSecret(),
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

    #[Pure]
    public function getUtils(): Utils
    {
        return new Utils($this);
    }

    public function createClient(): AccessTokenAwareClient
    {
        $httpClient = $this->getHttpClient();

        if ((bool) $this->config->get('http.retry', false)) {
            $httpClient = new RetryableHttpClient(
                $httpClient,
                $this->getRetryStrategy(),
                (int) $this->config->get('http.max_retries', 2) // @phpstan-ignore-line
            );
        }

        return (new AccessTokenAwareClient(
            client: $httpClient,
            accessToken: $this->getAccessToken(),
            failureJudge: fn (Response $response) => (bool) ($response->toArray()['errcode'] ?? 0),
            throw: (bool) $this->config->get('http.throw', true),
        ))->setPresets($this->config->all());
    }

    public function getRetryStrategy(): AccessTokenExpiredRetryStrategy
    {
        $retryConfig = RequestUtil::mergeDefaultRetryOptions((array) $this->config->get('http.retry', []));

        return (new AccessTokenExpiredRetryStrategy($retryConfig))
            ->decideUsing(function (AsyncContext $context, ?string $responseContent): bool {
                return ! empty($responseContent)
                    && str_contains($responseContent, '42001')
                    && str_contains($responseContent, 'access_token expired');
            });
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
}
