<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use Closure;
use EasyWeChat\BasicService;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support;
use EasyWeChat\OfficialAccount;

/**
 * @property \EasyWeChat\Pay\Bill\Client                  $bill
 * @property \EasyWeChat\Pay\Fundflow\Client              $fundflow
 * @property \EasyWeChat\Pay\Jssdk\Client                 $jssdk
 * @property \EasyWeChat\Pay\Order\Client                 $order
 * @property \EasyWeChat\Pay\Refund\Client                $refund
 * @property \EasyWeChat\Pay\Coupon\Client                $coupon
 * @property \EasyWeChat\Pay\Reverse\Client               $reverse
 * @property \EasyWeChat\Pay\Redpack\Client               $redpack
 * @property \EasyWeChat\BasicService\Url\Client          $url
 * @property \EasyWeChat\Pay\Transfer\Client              $transfer
 * @property \EasyWeChat\Pay\Security\Client              $security
 * @property \EasyWeChat\Pay\ProfitSharing\Client         $profit_sharing
 * @property \EasyWeChat\Pay\Contract\Client              $contract
 * @property \EasyWeChat\OfficialAccount\Auth\AccessToken $access_token
 *
 * @method mixed pay(array $attributes)
 * @method mixed authCodeToOpenid(string $authCode)
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected array $providers = [
        OfficialAccount\Auth\ServiceProvider::class,
        BasicService\Url\ServiceProvider::class,
        Base\ServiceProvider::class,
        Bill\ServiceProvider::class,
        Fundflow\ServiceProvider::class,
        Coupon\ServiceProvider::class,
        Jssdk\ServiceProvider::class,
        Merchant\ServiceProvider::class,
        Order\ServiceProvider::class,
        Redpack\ServiceProvider::class,
        Refund\ServiceProvider::class,
        Reverse\ServiceProvider::class,
        Sandbox\ServiceProvider::class,
        Transfer\ServiceProvider::class,
        Security\ServiceProvider::class,
        ProfitSharing\ServiceProvider::class,
        Contract\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected array $defaultConfig = [
        'http' => [
            'base_uri' => 'https://api.mch.weixin.qq.com/',
        ],
    ];

    /**
     * Build payment scheme for product.
     *
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

    /**
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @codeCoverageIgnore
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function handlePaidNotify(Closure $closure)
    {
        return (new Notify\Paid($this))->handle($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @codeCoverageIgnore
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function handleRefundedNotify(Closure $closure)
    {
        return (new Notify\Refunded($this))->handle($closure);
    }

    /**
     * @param \Closure $closure
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @codeCoverageIgnore
     *
     * @throws \EasyWeChat\Kernel\Exceptions\Exception
     */
    public function handleScannedNotify(Closure $closure)
    {
        return (new Notify\Scanned($this))->handle($closure);
    }

    /**
     * Set sub-merchant.
     *
     * @param string      $mchId
     * @param string|null $appId
     *
     * @return $this
     */
    public function setSubMerchant(string $mchId, string $appId = null)
    {
        $this['config']->set('sub_mch_id', $mchId);
        $this['config']->set('sub_appid', $appId);

        return $this;
    }

    /**
     * @return bool
     */
    public function inSandbox(): bool
    {
        return (bool) $this['config']->get('sandbox');
    }

    /**
     * @param string|null $endpoint
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function getKey(string $endpoint = null)
    {
        if ('sandboxnew/pay/getsignkey' === $endpoint) {
            return $this['config']->key;
        }

        $key = $this->inSandbox() ? $this['sandbox']->getKey() : $this['config']->key;

        if (empty($key)) {
            throw new InvalidArgumentException('config key should not empty.');
        }

        if (32 !== strlen($key)) {
            throw new InvalidArgumentException(sprintf("'%s' should be 32 chars length.", $key));
        }

        return $key;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this['base'], $name], $arguments);
    }
}
