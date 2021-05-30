<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\ApiBuilder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Application
{
    /**
     * @var array
     */
    public const DEFAULT_HTTP_OPTIONS = [
        'base_uri' => 'https://api.mch.weixin.qq.com/',
    ];

    protected ?ApiBuilder $v2 = null;
    protected ?ApiBuilder $v3 = null;
    protected ?HttpClientInterface $client = null;
    protected ?Merchant $merchant = null;
    protected array $config = [
        'mch_id' => 0,
        'secret_key' => '',
        'private_key' => '',
        'certificate' => '',
        'certificate_serial_no' => '',
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

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

    public function getClient(): HttpClientInterface
    {
        if (!$this->client) {
            $this->client = (new Client($this->getMerchant()))
                ->withOptions(\array_merge(self::DEFAULT_HTTP_OPTIONS, $this->config['http'] ?? []));
        }

        return $this->client;
    }

    public function v3(): ApiBuilder
    {
        if (!$this->v3) {
            $this->v3 = new ApiBuilder($this->getClient(), '/v3/');
        }

        return $this->v3;
    }

    public function v2(): ApiBuilder
    {
        if (!$this->v2) {
            $this->v2 = new ApiBuilder($this->getClient(), '/');
        }

        return $this->v2;
    }
}
