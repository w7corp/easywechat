<?php
/**
 * Merchant.php
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
 * Class Merchant.
 */
class Merchant extends Attribute
{
    /**
     * @var array
     */
    protected $attributes = [
        'app_id',
        'app_secret',
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
        'app_id'      => 'appid',
        'app_secret'  => 'secret',
        'merchant_id' => 'mch_id',
        'cert_path'   => 'sslcert_path',
        'key_path'    => 'sslkey_path',
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