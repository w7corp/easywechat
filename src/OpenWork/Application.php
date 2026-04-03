<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\Traits\InteractsWithWeChatApiClient;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithClient;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\SynchronizesServerRequest;
use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;
use EasyWeChat\OpenWork\Contracts\Application as ApplicationInterface;
use EasyWeChat\OpenWork\Contracts\SuiteTicket as SuiteTicketInterface;
use Overtrue\Socialite\Contracts\ProviderInterface as SocialiteProviderInterface;
use Overtrue\Socialite\Providers\OpenWeWork;
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
    use SynchronizesServerRequest;

    protected ?ServerInterface $server = null;

    protected bool $usesCustomServer = false;

    protected ?AccountInterface $account = null;

    protected bool $usesCustomAccount = false;

    protected ?Encryptor $encryptor = null;

    protected bool $usesCustomEncryptor = false;

    protected ?SuiteEncryptor $suiteEncryptor = null;

    protected bool $usesCustomSuiteEncryptor = false;

    protected ?SuiteTicketInterface $suiteTicket = null;

    protected bool $usesCustomSuiteTicket = false;

    protected ?AccessTokenInterface $accessToken = null;

    protected bool $usesCustomProviderAccessToken = false;

    protected ?AccessTokenInterface $suiteAccessToken = null;

    protected bool $usesCustomSuiteAccessToken = false;

    protected ?AuthorizerAccessToken $authorizerAccessToken = null;

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

    public function getSuiteEncryptor(): SuiteEncryptor
    {
        if (! $this->suiteEncryptor) {
            $this->suiteEncryptor = new SuiteEncryptor(
                suiteId: $this->getAccount()->getSuiteId(),
                token: $this->getAccount()->getToken(),
                aesKey: $this->getAccount()->getAesKey(),
            );
            $this->usesCustomSuiteEncryptor = false;
        }

        return $this->suiteEncryptor;
    }

    public function setSuiteEncryptor(SuiteEncryptor $encryptor): static
    {
        $this->suiteEncryptor = $encryptor;
        $this->usesCustomSuiteEncryptor = true;

        return $this;
    }

    public function getServer(): Server|ServerInterface
    {
        if (! $this->server) {
            $this->server = new Server(
                encryptor: $this->getSuiteEncryptor(),
                providerEncryptor: $this->getEncryptor(),
                request: $this->getRequest(),
            );
            $this->usesCustomServer = false;

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
        $this->usesCustomServer = true;

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
            $this->usesCustomProviderAccessToken = false;
        }

        return $this->accessToken;
    }

    public function setProviderAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->accessToken = $accessToken;
        $this->usesCustomProviderAccessToken = true;
        $this->resetClient();

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
            $this->usesCustomSuiteAccessToken = false;
        }

        return $this->suiteAccessToken;
    }

    public function setSuiteAccessToken(AccessTokenInterface $accessToken): static
    {
        $this->suiteAccessToken = $accessToken;
        $this->usesCustomSuiteAccessToken = true;

        return $this;
    }

    public function getSuiteTicket(): SuiteTicketInterface
    {
        if (! $this->suiteTicket) {
            $this->suiteTicket = new SuiteTicket(
                suiteId: $this->getAccount()->getSuiteId(),
                cache: $this->getCache(),
            );
            $this->usesCustomSuiteTicket = false;
        }

        return $this->suiteTicket;
    }

    public function setSuiteTicket(SuiteTicketInterface $suiteTicket): SuiteTicketInterface
    {
        $this->suiteTicket = $suiteTicket;
        $this->usesCustomSuiteTicket = true;

        return $this->suiteTicket;
    }

    /**
     * @throws HttpException
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

    public function getAuthorizerAccessToken(
        string $corpId,
        string $permanentCode,
        ?AccessTokenInterface $suiteAccessToken = null
    ): AuthorizerAccessToken {
        $suiteAccessToken = $suiteAccessToken ?? $this->getSuiteAccessToken();

        return new AuthorizerAccessToken(
            corpId: $corpId,
            permanentCodeOrAccessToken: $permanentCode,
            suiteAccessToken: $suiteAccessToken,
            cache: $this->getCache(),
            httpClient: $this->getHttpClient(),
        );
    }

    public function createClient(): AccessTokenAwareClient
    {
        return $this->createErrcodeAwareClient($this->getProviderAccessToken());
    }

    public function getAuthorizerClient(string $corpId, string $permanentCode, ?AccessTokenInterface $suiteAccessToken = null): AccessTokenAwareClient
    {
        return $this->createErrcodeAwareClient(
            $this->getAuthorizerAccessToken($corpId, $permanentCode, $suiteAccessToken)
        );
    }

    public function getJsApiTicket(string $corpId, string $permanentCode, ?AccessTokenInterface $suiteAccessToken = null): JsApiTicket
    {
        return new JsApiTicket(
            corpId: $corpId,
            cache: $this->getCache(),
            httpClient: $this->getAuthorizerClient($corpId, $permanentCode, $suiteAccessToken),
        );
    }

    public function getOAuth(
        string $suiteId,
        ?AccessTokenInterface $suiteAccessToken = null
    ): SocialiteProviderInterface {
        $account = $this->getAccount();

        return $this->configureOpenWorkOAuthProvider(new OpenWeWork(array_filter([
            'client_id' => $suiteId,
            'suite_id' => $suiteId,
            'suite_secret' => $account->getSuiteSecret(),
            'redirect_url' => $this->config->get('oauth.redirect_url'),
            'base_url' => $this->config->get('http.base_uri'),
        ])), $suiteAccessToken)->scopes((array) $this->config->get('oauth.scopes', ['snsapi_base']));
    }

    public function getCorpOAuth(
        string $corpId,
        ?AccessTokenInterface $suiteAccessToken = null
    ): SocialiteProviderInterface {
        $account = $this->getAccount();

        return $this->configureOpenWorkOAuthProvider(new OpenWeWork(array_filter([
            'client_id' => $corpId,
            'suite_id' => $account->getSuiteId(),
            'suite_secret' => $account->getSuiteSecret(),
            'redirect_url' => $this->config->get('oauth.redirect_url'),
            'base_url' => $this->config->get('http.base_uri'),
        ])), $suiteAccessToken)->scopes((array) $this->config->get('oauth.scopes', ['snsapi_base']));
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

            if (! $this->usesCustomEncryptor) {
                $this->encryptor = null;
            }

            if (! $this->usesCustomSuiteEncryptor) {
                $this->suiteEncryptor = null;
            }

            if (! $this->usesCustomServer) {
                $this->server = null;
            }

            if (! $this->usesCustomSuiteTicket) {
                $this->suiteTicket = null;
            }
        }

        if (! $this->usesCustomProviderAccessToken) {
            $this->accessToken = null;
        }

        if (! $this->usesCustomSuiteAccessToken) {
            $this->suiteAccessToken = null;
        }
    }

    protected function refreshDerivedDependenciesAfterAccountUpdated(): void
    {
        if (! $this->usesCustomEncryptor) {
            $this->encryptor = null;
        }

        if (! $this->usesCustomSuiteEncryptor) {
            $this->suiteEncryptor = null;
        }

        if (! $this->usesCustomServer) {
            $this->server = null;
        }

        if (! $this->usesCustomProviderAccessToken) {
            $this->accessToken = null;
        }

        if (! $this->usesCustomSuiteAccessToken) {
            $this->suiteAccessToken = null;
        }

        if (! $this->usesCustomSuiteTicket) {
            $this->suiteTicket = null;
        }

        $this->resetClient();
    }

    protected function configureOpenWorkOAuthProvider(
        OpenWeWork $provider,
        ?AccessTokenInterface $suiteAccessToken = null,
    ): OpenWeWork {
        if ($suiteAccessToken) {
            $provider->withSuiteAccessToken($suiteAccessToken->getToken());
        }

        try {
            $provider->withSuiteTicket($this->getSuiteTicket()->getTicket());
        } catch (RuntimeException) {
        }

        return $provider;
    }
}
