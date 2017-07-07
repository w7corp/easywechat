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

use EasyWeChat\Kernel\Support\HasAttributes;

/**
 * Class Order.
 *
 * @author overtrue <i@overtrue.me>
 *
 * @property string $body
 * @property string $detail
 * @property string $attach
 * @property string $out_trade_no
 * @property string $fee_type
 * @property string $total_fee
 * @property string $spbill_create_ip
 * @property string $time_start
 * @property string $time_expire
 * @property string $goods_tag
 * @property string $notify_url
 * @property string $trade_type
 * @property string $product_id
 * @property string $limit_pay
 * @property string $openid
 * @property string $sub_openid
 * @property string $auth_code
 */
class Order
{
    use HasAttributes;

    const JSAPI = 'JSAPI';
    const NATIVE = 'NATIVE';
    const APP = 'APP';
    const MICROPAY = 'MICROPAY';
    const MWEB = 'MWEB';

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }
}
