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
 * Merchant.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Payment;

use EasyWeChat\Support\Attribute;

/**
 * Class Merchant.
 *
 * @property string $app_id
 * @property string $merchant_id
 * @property string $key
 * @property string $sub_app_id
 * @property string $sub_merchant_id
 * @property string $ssl_cert_path
 * @property string $ssl_key_path
 * @property string $fee_type
 * @property string $device_info
 */
class Merchant extends Attribute
{
    /**
     * @var array
     */
    protected $attributes = [
        'app_id',
        'merchant_id',
        'key',
        'sub_app_id',
        'sub_merchant_id',
        'ssl_cert_path',
        'ssl_key_path',
        'fee_type',
        'device_info',
    ];

    /**
     * Aliases of attributes.
     *
     * @var array
     */
    protected $aliases = [
        'app_id' => 'appid',
        'key' => 'mch_key',
        'merchant_id' => 'mch_id',
        'sub_app_id' => 'sub_appid',
        'sub_merchant_id' => 'sub_mch_id',
        'cert_path' => 'sslcert_path',
        'key_path' => 'sslkey_path',
    ];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        parent::__construct($attributes);

        $this->with('fee_type', 'CNY');
    }
}
