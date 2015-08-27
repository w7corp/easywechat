<?php
/**
 * OrderQuery.php
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

use Overtrue\Wechat\Http;
use Overtrue\Wechat\Util\Bag;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Config\WechatConfig;

class RefundQuery
{
    /**
     * 退款订单查询接口
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_5
     */
    const REFUNDQUERY_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';
    
    /**
     * 参数包
     * @var String(32)
     */
    protected $bag;

    /**
     * 商户 Key
     */
    protected $key;

     /**
     * 必填项目
     */
    protected $required = array('appid', 'mch_id', 'nonce_str');
    /**
     * 选填项目
     */
    protected $optional = array('transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id');

    /**
     * QueryOrder缓存
     * 
     * @var Array
     */
    protected $refundOrder = null;
    
    public function __construct(Bag $bag)
    {   
        //商户基本信息
        $bag->set('appid',  WechatConfig::APPID);
        $bag->set('mch_id', WechatConfig::MCHID);

        if( !$bag->has('nonce_str') ) {
            $bag->set('nonce_str', md5(uniqid(microtime())) );
        }

        // 检测必填字段
        foreach($this->required AS $paramName) {
            if( !$bag->has($paramName) ) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }

        if( !$bag->has('refund_id') && !$bag->has('out_refund_no') &&
                !$bag->has('transaction_id') && !$bag->has('out_trade_no') ) {

            throw new Exception('refund_id or out_refund_no or transaction_id or out_trade_no is required at least');
        }

        $this->bag = $bag;
        $this->key = WechatConfig::KEY;
    }

    /**
     * 退款订单查询结果
     * 
     * @param bool|false $force 是否忽略缓存强制更新
     *
     * @return array
     * @throws Exception
     * @throws \Overtrue\Wechat\Exception
     */
    public function getResponse($force = false)
    {

        if ($this->refundOrder !== null && $force === false) {
            return $this->refundOrder;
        }
        
        $params  =  $this->bag->all();

        $signGenerator = new SignGenerator($params);
        $signGenerator->onSortAfter(function($that) {
            $that->key = $this->key;
        });

        // 生成签名
        $sign = $signGenerator->getResult();

        // 调置签名
        $params['sign'] = $sign;

        $request = XML::build($params);
        
        // $http = new Http(new AccessToken($this->business->appid, $this->business->appsecret));
        $http = new Http();
        
        $response = $http->request(static::REFUNDQUERY_URL, Http::POST, $request);
        if(empty($response)) {
            throw new Exception('Get RefundQuery Failure:');
        }

        $response = XML::parse($response);
        if( isset($response['result_code']) &&
            ($response['result_code'] === 'FAIL') ) {
            throw new Exception($response['err_code'].': '.$response['err_code_des']);
        }
        if( isset($response['return_code']) &&
            $response['return_code'] === 'FAIL' ) {
            throw new Exception($response['return_code'].': '.$response['return_msg']);
        }

        return $this->refundOrder = $response;
    }
}
