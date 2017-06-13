<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ServiceProvider.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @see      https://github.com/overtrue/wechat
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Applications\OfficialAccount\Payment;

use EasyWeChat\Applications\OfficialAccount\Payment\CashCoupon\CashCoupon;
use EasyWeChat\Applications\OfficialAccount\Payment\LuckyMoney\LuckyMoney;
use EasyWeChat\Applications\OfficialAccount\Payment\MerchantPay\MerchantPay;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $container)
    {
        $container['merchant'] = function ($container) {
            $config = array_merge(
                ['app_id' => $container['config']['app_id']],
                $container['config']->get('payment', [])
            );

            return new Merchant($config);
        };

        $container['payment'] = function ($container) {
            $payment = new Payment($container['merchant']);
            $payment->sandboxMode(
                (bool) $container['config']->get('payment.sandbox_mode')
            );

            return $payment;
        };

        $container['lucky_money'] = function ($container) {
            return new LuckyMoney($container['merchant']);
        };

        $container['merchant_pay'] = function ($container) {
            return new MerchantPay($container['merchant']);
        };

        $container['cash_coupon'] = function ($container) {
            return new CashCoupon($container['merchant']);
        };
    }
}
