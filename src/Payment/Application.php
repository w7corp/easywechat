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

use EasyWeChat\BaseService;
use EasyWeChat\Kernel\ServiceContainer;

/**
 * Class Application.
 *
 * @property \EasyWeChat\Payment\Coupon\Client   $coupon
 * @property \EasyWeChat\Payment\Redpack\Client  $redpack
 * @property \EasyWeChat\Payment\Transfer\Client $transfer
 * @property \EasyWeChat\Payment\Jssdk\Client    $jssdk
 *
 * @method \EasyWeChat\Payment\Client sandboxMode(bool $enabled = false)
 * @method string scheme(string $productId)
 * @method mixed pay(\EasyWeChat\Payment\Order $order)
 * @method mixed prepare(\EasyWeChat\Payment\Order $order)
 * @method mixed query(string $orderNo)
 * @method mixed queryByTransactionId(string $transactionId)
 * @method mixed close(string $tradeNo)
 * @method mixed reverse(string $orderNo)
 * @method mixed reverseByTransactionId(string $transactionId)
 */
class Application extends ServiceContainer
{
    /**
     * @var array
     */
    protected $providers = [
        BaseService\Url\ServiceProvider::class,
        ServiceProvider::class,
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
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this['payment'], $name], $arguments);
    }
}
