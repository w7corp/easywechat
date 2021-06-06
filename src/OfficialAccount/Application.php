<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\Kernel\ApiBuilder;
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

    public function __construct(
        protected string $appId,
        protected string $secret,
        protected string $aesKey,
        protected string $token,
    ) {}

    public function getAccount(): Account
    {
        $this->account || $this->account = new Account(
            $this->appId,
            $this->secret,
            $this->aesKey,
            $this->token
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
}
