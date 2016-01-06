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
 * Order.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Frye <frye0423@gmail.com>
 * @copyright 2015 Frye <frye0423@gmail.com>
 *
 * @link      https://github.com/0i
 * @link      http://blog.lost-magic.com
 * @link      https://github.com/thenbsp/Wechat
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Utils\MagicAttributes;

class Order extends MagicAttributes
{
    /**
     * 订单必填项.
     * 
     * @var array
     */
    protected static $required = array('body', 'out_trade_no', 'total_fee', 'notify_url');

    /**
     * 订单选填项.
     * 
     * @var array
     */
    protected static $optional = array(
        'device_info', 'detail', 'attach', 'fee_type', 'time_start', 'time_expire',
        'goods_tag', 'product_id', 'limit_pay', 'nonce_str', 'spbill_create_ip',
        'trade_type', 'openid',
    );

    /**
     * @var array
     */
    protected static $params = null;

    public function __construct()
    {
        if (static::$params === null) {
            static::$params = array_merge(static::$required, static::$optional);
        }
    }

    /**
     * 检测参数值是否有效.
     * 
     * @throws Exception
     */
    public function checkParams()
    {
        foreach (static::$required as $paramName) {
            if (empty($this->attributes[$paramName])) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }
    }

    public function __set($property, $value)
    {
        if (!in_array($property, static::$params)) {
            throw new Exception(sprintf('"%s" is required', $property));
        }

        return parent::__set($property, $value);
    }
}
