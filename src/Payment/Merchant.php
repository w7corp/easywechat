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
 * Class Merchant.
 *
 * @author overtrue <i@overtrue.me>
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
class Merchant
{
    use HasAttributes;

    /**
     * @var array
     */
    // protected $attributes = [
    //     'app_id',
    //     'merchant_id',
    //     'key',
    //     'sub_app_id',
    //     'sub_merchant_id',
    //     'ssl_cert_path',
    //     'ssl_key_path',
    //     'fee_type',
    //     'device_info',
    // ];

    /**
     * Aliases of attributes.
     *
     * @var array
     */
    // protected $aliases = [
    //     'app_id' => 'appid',
    //     'key' => 'mch_key',
    //     'merchant_id' => 'mch_id',
    //     'sub_app_id' => 'sub_appid',
    //     'sub_merchant_id' => 'sub_mch_id',
    //     'cert_path' => 'sslcert_path',
    //     'key_path' => 'sslkey_path',
    // ];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->setAttributes($attributes);

        $this->aliases = [
            'app_id' => 'appid',
            'key' => 'mch_key',
            'merchant_id' => 'mch_id',
            'sub_app_id' => 'sub_appid',
            'sub_merchant_id' => 'sub_mch_id',
            'cert_path' => 'sslcert_path',
            'key_path' => 'sslkey_path',
        ];

        $this->with('fee_type', 'CNY');
    }
}
