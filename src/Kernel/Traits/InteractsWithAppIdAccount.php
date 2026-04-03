<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;

trait InteractsWithAppIdAccount
{
    /**
     * @template TAccount of object
     *
     * @param  class-string<TAccount>  $accountClass
     * @return TAccount
     */
    protected function createAppIdAccount(string $accountClass): object
    {
        return new $accountClass(
            appId: (string) $this->config->get('app_id'), /** @phpstan-ignore-line */
            secret: (string) $this->config->get('secret'), /** @phpstan-ignore-line */
            token: (string) $this->config->get('token'), /** @phpstan-ignore-line */
            aesKey: (string) $this->config->get('aes_key'), /** @phpstan-ignore-line */
        );
    }

    /**
     * @throws InvalidConfigException
     */
    protected function createAppIdEncryptor(
        string $appId,
        ?string $token,
        ?string $aesKey,
        bool $requireCredentials = true,
        ?string $receiveId = null,
    ): Encryptor {
        if ($requireCredentials && (empty($token) || empty($aesKey))) {
            throw new InvalidConfigException('token or aes_key cannot be empty.');
        }

        return new Encryptor(
            appId: $appId,
            token: (string) $token,
            aesKey: (string) $aesKey,
            receiveId: $receiveId ?? $appId,
        );
    }

    /**
     * @template TServer of object
     *
     * @param  class-string<TServer>  $serverClass
     * @return TServer
     */
    protected function createAppIdServer(string $serverClass, ?Encryptor $encryptor): object
    {
        return new $serverClass(
            request: $this->getRequest(),
            encryptor: $encryptor,
        );
    }
}
