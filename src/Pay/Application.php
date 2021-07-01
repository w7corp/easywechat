<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Traits\InteractWithConfig;
use EasyWeChat\Kernel\Client;
use EasyWeChat\Kernel\Contracts\Config as ConfigInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Application implements \EasyWeChat\Pay\Contracts\Application
{
    use InteractWithConfig;

    public const DEFAULT_HTTP_OPTIONS = [
        'base_uri' => 'https://api.mch.weixin.qq.com/',
    ];

    protected ?Client $v2Client = null;
    protected ?Client $v3Client = null;
    protected ?HttpClientInterface $httpClient = null;
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

    public function getHttpClient(): HttpClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = (new HttpClient($this->getMerchant()))
                ->withOptions(\array_merge(self::DEFAULT_HTTP_OPTIONS, $this->config['http'] ?? []));
        }

        return $this->httpClient;
    }

    public function getClient(): Client
    {
        if (!$this->v3Client) {
            $this->v3Client = new Client(uri: '/v3/', client: $this->getHttpClient());
        }

        return $this->v3Client;
    }

    public function getV2Client(): Client
    {
        if (!$this->v2Client) {
            $this->v2Client = new Client(uri: '/', client: $this->getHttpClient());
        }

        return $this->v2Client;
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
