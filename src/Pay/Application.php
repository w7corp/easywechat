<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\BasicService;
use EasyWeChat\Kernel\ApiBuilder;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support;
use EasyWeChat\OfficialAccount;
use JetBrains\PhpStorm\Pure;
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
    protected Merchant $merchant;
    protected array $config = [
        'app_id' => null,
        'secret_key' => '',
        'private_key' => '',
        'certificate' => '',
        'certificate_serial_no' => '',
    ];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getMerchant()
    {
        if (!$this->merchant) {
            $this->merchant = new Merchant(
                appId: $this->config['app_id'],
                secretKey: $this->config['secret_key'],
                privateKey: $this->config['private_key'],
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
