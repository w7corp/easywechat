<?php
/**
 * RefundQuery.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    peiwen <haopeiwen123@gmail.com>
 * @copyright 2015 peiwen <haopeiwen123@gmail.com>
 * @link      https://github.com/troubleman
 * @link      https://github.com/troubleman/Wechat
 */

namespace Overtrue\Wechat\Payment;

class RefundQuery extends Order
{
    // 退款订单查询接口
    public $url = 'https://api.mch.weixin.qq.com/pay/refundquery';

     /**
     * 必填项目
     */
    protected $required = array('appid', 'mch_id', 'nonce_str');

    public function __construct(Bag $bag, $key)
    {   
        
        if (!$bag->has('nonce_str')) {
            $bag->set('nonce_str', md5(uniqid(microtime())));
        }

        if (!$bag->has('refund_id') && !$bag->has('out_refund_no') &&
                !$bag->has('transaction_id') && !$bag->has('out_trade_no')) {
            throw new Exception('query order_no is required as least');
        }

        parent::__construct($bag, $key);
    }
}
