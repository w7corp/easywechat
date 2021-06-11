<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\ApiBuilder;
use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\OfficialAccount\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationInterface;
use EasyWeChat\OfficialAccount\Contracts\HttpClient as HttpClientInterface;
use EasyWeChat\OfficialAccount\Contracts\Server as ServerInterface;
use EasyWeChat\OfficialAccount\Contracts\Request as RequestInterface;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\OfficialAccount\Server\Request;
use EasyWeChat\OfficialAccount\Server\Server;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class Application implements ApplicationInterface
{
    protected ?ApiBuilder $client = null;
    protected ?Encryptor $encryptor = null;
    protected ?ServerInterface $server = null;
    protected ?CacheInterface $cache = null;
    protected ?ConfigInterface $config = null;
    protected ?AccountInterface $account = null;
    protected ?RequestInterface $request = null;
    protected ?AccessTokenInterface $accessToken = null;
    protected ?HttpClientInterface $httpClient = null;

    /**
     * @var array
     */
    public const DEFAULT_HTTP_OPTIONS = [
        'timeout' => 30.0,
        'base_uri' => 'https://api.mch.weixin.qq.com/',
    ];

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function __construct(array | ConfigInterface $config)
    {
        if (\is_array($config)) {
            $config = new Config($config);
        }

        $this->config = $config;
    }

    public function getAccount(): AccountInterface
    {
        if (!$this->account) {
            $this->account = new Account(
                appId: $this->config->get('app_id'),
                secret: $this->config->get('secret'),
                aesKey: $this->config->get('aes_key'),
                token: $this->config->get('token')
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
        $this->encryptor || $this->encryptor = new Encryptor(
            $this->account->getAppId(),
            $this->account->getToken(),
            $this->account->getAesKey(),
        );

        return $this->encryptor;
    }

    public function setEncryptor(Encryptor $encryptor): static
    {
        $this->encryptor = $encryptor;

        return $this;
    }

    public function getRequest(): Request
    {
        if (!$this->request) {
            $this->request = Request::capture();
        }

        return $this->request;
    }

    public function setRequest(RequestInterface $request): static
    {
        $this->request = $request;

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
            $this->server = new Server($this);
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;

        return $this;
    }

    public function getClient(): ApiBuilder
    {
        if (!$this->client) {
            $this->client = new ApiBuilder($this->getHttpClient()->withAccessToken($this->getAccessToken()));
        }

        return $this->client;
    }

    public function getHttpClient(): HttpClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = (new HttpClient())
                ->withOptions(\array_merge(self::DEFAULT_HTTP_OPTIONS, $this->config->get('http', [])));
        }

        return $this->httpClient;
    }

    public function getAccessToken(): AccessToken
    {
        if (!$this->accessToken) {
            $this->accessToken = new AccessToken(
                $this->getAccount(),
                $this->getClient(),
                $this->getCache(),
            );
        }

        return $this->accessToken;
    }

    public function setCache(CacheInterface $cache): static
    {
        $this->cache = $cache;

        return $this;
    }

    public function getCache(): CacheInterface
    {
        if (!$this->cache) {
            $this->cache = new Psr16Cache(
                new FilesystemAdapter(
                    $this->config->get('cache.namespace', 'easywechat'),
                    $this->config->get('cache.lifetime', 1500),
                )
            );
        }

        return $this->cache;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function setConfig(ConfigInterface $config): static
    {
        $this->config = $config;

        return $this;
    }
}
