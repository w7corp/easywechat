<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Payment;

use EasyWeChat\BasicService;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support;
use EasyWeChat\OfficialAccount;

/**
 * Class Application.
 *
 * @property \EasyWeChat\Payment\Bill\Client               $bill
 * @property \EasyWeChat\Payment\Jssdk\Client              $jssdk
 * @property \EasyWeChat\Payment\Order\Client              $order
 * @property \EasyWeChat\Payment\Refund\Client             $refund
 * @property \EasyWeChat\Payment\Coupon\Client             $coupon
 * @property \EasyWeChat\Payment\Reverse\Client            $reverse
 * @property \EasyWeChat\Payment\Redpack\Client            $redpack
 * @property \EasyWeChat\BasicService\Url\Client           $url
 * @property \EasyWeChat\Payment\Transfer\Client           $transfer
 * @property \EasyWeChat\OfficialAccount\Auth\AccessToken  $access_token
 *
 * @method mixed pay(array $attributes)
 * @method mixed authCodeToOpenId(string $authCode)
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        OfficialAccount\Auth\ServiceProvider::class,
        BasicService\Url\ServiceProvider::class,
        Base\ServiceProvider::class,
        Bill\ServiceProvider::class,
        Coupon\ServiceProvider::class,
        Jssdk\ServiceProvider::class,
        Order\ServiceProvider::class,
        Redpack\ServiceProvider::class,
        Refund\ServiceProvider::class,
        Reverse\ServiceProvider::class,
        Sandbox\ServiceProvider::class,
        Transfer\ServiceProvider::class,
    ];

    /**
     * @var array
     */
    protected $defaultConfig = [
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
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handlePaidNotify(callable $callback)
    {
        return (new Notify\Paid($this))->handle($callback);
    }

    /**
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleRefundedNotify(callable $callback)
    {
        return (new Notify\Refunded($this))->handle($callback);
    }

    /**
     * @param callable $callback
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleScannedNotify(callable $callback)
    {
        return (new Notify\Scanned($this))->handle($callback);
    }

    /**
     * @return bool
     */
    public function inSandbox(): bool
    {
        return (bool) $this['config']->get('sandbox');
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
