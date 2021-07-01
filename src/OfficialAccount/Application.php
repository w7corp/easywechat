<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\Traits\InteractWithAccessTokenClient;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationInterface;
use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as HttpClientInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use Overtrue\Socialite\Providers\WeChat;

class Application implements ApplicationInterface
{
    use InteractWithConfig;
    use InteractWithCache;
    use InteractWithServerRequest;
    use InteractWithHttpClient;
    use InteractWithAccessTokenClient;

    protected ?Encryptor $encryptor = null;
    protected ?ServerInterface $server = null;
    protected ?AccountInterface $account = null;
    protected ?AccessTokenInterface $accessToken = null;
    protected ?\Closure $oauthFactory;

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

    public function getAccessToken(): AccessTokenInterface
    {
        if (!$this->accessToken) {
            $this->accessToken = new AccessToken(
                appId: $this->getAccount()->getAppId(),
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

    public function setOAuthFactory(callable $factory): static
    {
        $this->oauthFactory = fn ($app) => $factory($app);

        return $this;
    }

    public function getOAuth(): WeChat
    {
        if (!$this->oauthFactory) {
            $this->oauthFactory = fn () => (new WeChat(
                [
                    'client_id' => $this->getAccount()->getAppId(),
                    'client_secret' => $this->getAccount()->getSecret(),
                    'redirect_url' => $this->config->get('oauth.redirect_url'),
                ]
            ))->scopes($this->config->get('oauth.scopes', ['snsapi_userinfo']));
        }

        return \call_user_func($this->oauthFactory, $this);
    }
}
