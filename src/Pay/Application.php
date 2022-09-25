<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Application implements \EasyWeChat\Pay\Contracts\Application
{
    use InteractWithConfig;
    use InteractWithHttpClient;
    use InteractWithServerRequest;

    protected ?ServerInterface $server = null;

    protected ?HttpClientInterface $client = null;

    protected ?Merchant $merchant = null;

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getUtils(): Utils
    {
        return new Utils($this->getMerchant());
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getMerchant(): Merchant
    {
        if (! $this->merchant) {
            $this->merchant = new Merchant(
                mchId: $this->config['mch_id'], /** @phpstan-ignore-line */
                privateKey: new PrivateKey((string) $this->config['private_key']), /** @phpstan-ignore-line */
                certificate: new PublicKey((string) $this->config['certificate']), /** @phpstan-ignore-line */
                secretKey: (string) $this->config['secret_key'], /** @phpstan-ignore-line */
                v2SecretKey: (string) $this->config['v2_secret_key'], /** @phpstan-ignore-line */
                platformCerts: $this->config->has('platform_certs') ? (array) $this->config['platform_certs'] : [],/** @phpstan-ignore-line */
            );
        }

        return $this->merchant;
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
                merchant: $this->getMerchant(),
                request: $this->getRequest(),
            );
        }

        return $this->server;
    }

    public function setServer(ServerInterface $server): static
    {
        $this->server = $server;

        return $this;
    }

    public function setConfig(ConfigInterface $config): static
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getClient(): HttpClientInterface
    {
        return $this->client ?? $this->client = (new Client(
            $this->getMerchant(),
            $this->getHttpClient(),
            (array) $this->config->get('http', [])
        ))->setPresets($this->config->all());
    }

    public function setClient(HttpClientInterface $client): static
    {
        $this->client = $client;

        return $this;
    }
}
