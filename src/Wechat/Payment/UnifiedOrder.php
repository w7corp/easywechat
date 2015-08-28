<?php
/**
 * UnifiedOrder.php
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

use Overtrue\Wechat\Utils\JSON;

class UnifiedOrder extends Order
{
    // 统一下单接口
    public $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

     /**
     * 必填项目
     */
    protected $required = array(
        'appid', 'mch_id', 'nonce_str', 'body', 'out_trade_no','total_fee', 'spbill_create_ip',
         'notify_url', 'trade_type' 
    );

    
    public function __construct(Bag $bag, $key)
    {   
        
        if (!$bag->has('nonce_str')) {
            $bag->set('nonce_str', md5(uniqid(microtime())));
        }

        //操作员 默认为商户号
        if (!$bag->has('spbill_create_ip')) {

            $spbill_create_ip = empty($_SERVER['REMOTE_ADDR']) ? '0.0.0.0' : $_SERVER['REMOTE_ADDR'];
            
            $bag->set('spbill_create_ip', $spbill_create_ip);
        }

        if (!$bag->has('trade_type')) {
            if (!$bag->has('openid')) {
                throw new Exception('openid is required');
            }
            $bag->set('trade_type', 'JSAPI');
        }

        parent::__construct($bag, $key);
    }

}
