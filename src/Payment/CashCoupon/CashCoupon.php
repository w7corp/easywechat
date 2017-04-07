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
 * LuckyMoney.php.
 *
 * @author    tianyong90 <412039588@qq.com>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Payment\CashCoupon;

use EasyWeChat\Payment\Merchant;

/**
 * Class LuckyMoney.
 */
class CashCoupon
{
    /**
     * @var API
     */
    protected $api;

    /**
     * Merchant instance.
     *
     * @var \EasyWeChat\Payment\Merchant
     */
    protected $merchant;

    /**
     * Constructor.
     *
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Merchant setter.
     *
     * @param Merchant $merchant
     */
    public function setMerchant(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * Merchant getter.
     *
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * API setter.
     *
     * @param API $api
     */
    public function setAPI(API $api)
    {
        $this->api = $api;
    }

    /**
     * Return API instance.
     *
     * @return API
     */
    public function getAPI()
    {
        return $this->api ?: $this->api = new API($this->getMerchant());
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->getAPI(), $method])) {
            return call_user_func_array([$this->api, $method], $args);
        }
    }
}
