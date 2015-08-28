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
use Overtrue\Wechat\Payment\Business;

class OrderQuery
{
    // 订单查询接口
    const ORDERQUERY_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';

    // 退款订单查询接口
    const REFUNDQUERY_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';

    /**
     * 参数包
     * @var String(32)
     */
    protected $bag;

    /**
     * 商户
     */
    protected $business;

     /**
     * 必填项目
     */
    protected $required = array('appid', 'mch_id', 'nonce_str');
    /**
     * 选填项目
     */
    protected $optional = array('transaction_id', 'out_trade_no');

    /**
     * QueryOrder缓存
     * 
     * @var Array
     */
    protected $queryOrder = null;
    
    public function __construct(Bag $bag, Business $business)
    {   
        //商户基本信息
        $bag->set('appid',  $business->appid);
        $bag->set('mch_id', $business->mch_id);

        if (!$bag->has('nonce_str')) {
            $bag->set('nonce_str', md5(uniqid(microtime())));
        }

        // 检测必填字段
        foreach($this->required as $paramName) {
            if (!$bag->has($paramName)) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }

        if (!$bag->has('refund_id') && !$bag->has('out_refund_no') &&
                !$bag->has('transaction_id') && !$bag->has('out_trade_no')) {
            throw new Exception('query order_no is required as least');
        }

        $this->bag = $bag;
        $this->business = $business;
    }

    /**
     * 获取订单查询结果
     * 
     * @param bool|false $type 订单类型，默认为付款订单查询
     *
     * @return array
	 *
     * @throws Exception
     * @throws \Overtrue\Wechat\Exception
     */
    public function getResponse($type = null)
    {
        $params  =  $this->bag->all();

        $signGenerator = new SignGenerator($params);
        $signGenerator->onSortAfter(function($that) {
            $that->key = $this->business->mch_key;
        });

        // 生成签名
        $sign = $signGenerator->getResult();

        // 调置签名
        $params['sign'] = $sign;

        $request = XML::build($params);
        
        $http = new Http();

        $url = ($type !== 'refund') ? static::ORDERQUERY_URL : static::REFUNDQUERY_URL;

        $response = $http->request($url, Http::POST, $request);

        if (empty($response)) {
            throw new Exception('Get OrderQuery Failure:');
        }

        $response = XML::parse($response);
        if (isset($response['result_code']) &&
            ($response['result_code'] === 'FAIL')) {
            throw new Exception($response['err_code'].': '.$response['err_code_des']);
        }
        if (isset($response['return_code']) &&
            $response['return_code'] === 'FAIL') {
            throw new Exception($response['return_code'].': '.$response['return_msg']);
        }

        return $this->queryOrder = $response;
    }
}
