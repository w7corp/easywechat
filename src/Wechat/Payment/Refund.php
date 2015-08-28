<?php
/**
 * Refund.php
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

class Refund extends Order
{
    // 申请退款接口
    public $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

     /**
     * 必填项目
     */
    protected $required = array(
        'appid', 'mch_id', 'nonce_str', 'out_refund_no','total_fee', 'refund_fee', 'op_user_id'
    );

    
    public function __construct(Bag $bag, $key)
    {   
        
        if (!$bag->has('nonce_str')) {
            $bag->set('nonce_str', md5(uniqid(microtime())));
        }

        //操作员 默认为商户号
        if (!$bag->has('op_user_id')) {
            $bag->set('op_user_id', $bag->get('mch_id'));
        }

        if (!$bag->has('transaction_id') && !$bag->has('out_trade_no')) {
            throw new Exception('query order_no is required as least');
        }

        parent::__construct($bag, $key);
    }

}
