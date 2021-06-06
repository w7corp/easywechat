<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\ApiBuilder;
use EasyWeChat\Kernel\Config;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationContract;
use EasyWeChat\OfficialAccount\Contracts\Server as ServerContract;
use EasyWeChat\OfficialAccount\Contracts\Request as RequestContract;
use EasyWeChat\OfficialAccount\Server\Request;
use EasyWeChat\OfficialAccount\Server\Response;
use EasyWeChat\OfficialAccount\Server\Server;

class Application implements ApplicationContract
{
    protected ?Account $account = null;
    protected ?RequestContract $request = null;
    protected ?ServerContract $server = null;
    protected ?Encryptor $encryptor = null;
    protected ?Config $config = null;

    public function __construct(
        public array $userConfig
    ) {}

    public function getAccount(): Account
    {
        $this->account || $this->account = new Account(
            $this->getConfig()->get('appId'),
            $this->getConfig()->get('secret'),
            $this->getConfig()->get('aesKey'),
            $this->getToken()
        );

        return $this->account;
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
        $this->request || $this->request = Request::capture();

        return $this->request;
    }

    public function setRequest(RequestContract $request): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @throws \ReflectionException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \Throwable
     */
    public function getServer(): ServerContract
    {
        $this->server || $this->server = new Server($this);

        return $this->server;
    }

    public function getClient(): ApiBuilder
    {
        // TODO: Implement getClient() method.
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function reply(array $attributes, $appends = []): Contracts\Response
    {
        return Response::replay($attributes, $this, $appends);
    }

    public function getConfig(): Config
    {
        if ($this->config) {
            return $this->config;
        }

        $baseConfig = [
            // http://docs.guzzlephp.org/en/stable/request-options.html
            'http' => [
                'timeout' => 30.0,
                'base_uri' => 'https://api.weixin.qq.com/',
            ],
            'cache' => [
                'namespace' => 'easywechat',
                'life_time' => 1500,
            ],
        ];

        return new Config(array_replace_recursive($baseConfig, $this->userConfig));
    }

    public function getToken(): string
    {
        //
    }
}
