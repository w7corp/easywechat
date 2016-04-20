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
 * Order.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Payment;

use EasyWeChat\Support\Attribute;

/**
 * Class Order.
 */
class Order extends Attribute
{
    const JSAPI = 'JSAPI';
    const NATIVE = 'NATIVE';
    const APP = 'APP';
    const MICROPAY = 'MICROPAY';

    protected $attributes = [
        'body',
        'detail',
        'attach',
        'out_trade_no',
        'fee_type',
        'total_fee',
        'spbill_create_ip',
        'time_start',
        'time_expire',
        'goods_tag',
        'notify_url',
        'trade_type',
        'product_id',
        'limit_pay',
        'openid',
        'sub_openid',
        'auth_code',
    ];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct($attributes);

        $this->with('spbill_create_ip', get_client_ip());
    }
}
