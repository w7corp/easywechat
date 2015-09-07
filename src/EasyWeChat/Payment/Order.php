<?php

/**
 * Order.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
}
