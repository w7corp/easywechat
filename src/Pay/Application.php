<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use EasyWeChat\Kernel\Traits\InteractWithHttpClient;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Application implements \EasyWeChat\Pay\Contracts\Application
{
    use InteractWithConfig;
    use InteractWithHttpClient;
    use InteractWithServerRequest;

    protected ?ServerInterface $server = null;
    protected ?HttpClientInterface $v2Client = null;
    protected ?HttpClientInterface $v3Client = null;
    protected ?Merchant $merchant = null;

    public function getMerchant(): Merchant
    {
        if (!$this->merchant) {
            $this->merchant = new Merchant(
                mchId: $this->config['mch_id'],
                privateKey: $this->config['private_key'],
                secretKey: $this->config['secret_key'],
                certificate: $this->config['certificate'],
                certificateSerialNo: $this->config['certificate_serial_no'],
            );
        }

        return $this->merchant;
    }

    public function decorateMerchantAwareHttpClient(HttpClientInterface $httpClient): MerchantAwareHttpClient
    {
        return new MerchantAwareHttpClient($this->getMerchant(), $httpClient, $this->config->get('http', []));
    }

    public function getClient(): HttpClientInterface
    {
        if (!$this->v3Client) {
            $this->v3Client = $this->decorateMerchantAwareHttpClient($this->getHttpClient())->withUri('v3');
        }

        return $this->v3Client;
    }

    public function getV2Client(): HttpClientInterface
    {
        if (!$this->v2Client) {
            $this->v2Client = $this->decorateMerchantAwareHttpClient($this->getHttpClient());
        }

        return $this->v2Client;
    }

    public function getJssdk(): Jssdk
    {
        return new Jssdk($this->getMerchant());
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
}
