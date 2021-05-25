<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use Closure;
use EasyWeChat\BasicService;
use EasyWeChat\Kernel\ApiBuilder;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support;
use EasyWeChat\OfficialAccount;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected array $providers = [
        OfficialAccount\Auth\ServiceProvider::class,
        BasicService\Url\ServiceProvider::class,
    ];

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

    public function __construct(array $config = [], array $prepends = [], string $id = null)
    {
        parent::__construct($config, $prepends, $id);

        $this->merchant = new Merchant(
            appId: $config['app_id'],
            secretKey: $config['secret_key'],
            privateKey: $config['private_key'],
            certificate: $config['certificate'],
            certificateSerialNo: $config['certificate_serial_no'],
        );
    }

    public function getClient(): HttpClientInterface
    {
        if (!$this->client) {
            $this->client = (new Client($this->merchant))
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

    /**
     * @param string $productId
     *
     * @return string
     */
    public function scheme(string $productId): string
    {
        $params = [
            'appid' => $this['config']->app_id,
            'mch_id' => $this['config']->mch_id,
            'time_stamp' => time(),
            'nonce_str' => uniqid(),
            'product_id' => $productId,
        ];

        $params['sign'] = Support\generate_sign($params, $this['config']->key);

        return 'weixin://wxpay/bizpayurl?'.http_build_query($params);
    }

    /**
     * @param string $codeUrl
     *
     * @return string
     */
    public function codeUrlScheme(string $codeUrl)
    {
        return \sprintf('weixin://wxpay/bizpayurl?sr=%s', $codeUrl);
    }
}
