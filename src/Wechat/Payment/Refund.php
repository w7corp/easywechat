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
 * Refund.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    jaring <pengjiayin@gmail.com>
 *
 *
 *Usage:
 *   $business = new Business($appId, $appSecret, $mchId, $mchKey);
 *   $business->setClientCert(dirname(__FILE__).'/cert/apiclient_cert.pem');
 *   $business->setClientKey(dirname(__FILE__).'/cert/apiclient_key.pem');
 *   $refund =new Refund($business);
 *   $refund->out_refund_no= md5(uniqid(microtime()));//退单单号
 *   $refund->total_fee=1; //订单金额
 *   $refund->refund_fee=1;//退款金额
 *   $refund->out_trade_no=$order_id;//原商户订单号
 *   var_dump($trans->getResponse());
*/

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Http;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Utils\XML;

class Refund
{
    /**
     * 退款接口链接：https://api.mch.weixin.qq.com/secapi/pay/refund.
     */
    const REFUNDORDER_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

    /**
     * 商户信息.
     * 
     * @var Business
     */
    protected $business;

    /**
     * 退款订单必填项.
     *
     * @var array
     */
    protected static $required = array('out_refund_no', 'total_fee', 'refund_fee');

    /**
     * 退款订单选填项.
     *
     * @var array
     */
    protected static $optional = array('out_trade_no', 'transaction_id', 'device_info', 'fee_type', 'op_user_id');

    /**
     * @var array
     */
    protected static $params  = null;
    protected static $allowParams = array();

    /**
     * 退款返回信息.
     *
     * @var array
     */
    protected $refundInfo = null;

    public function __construct(Business $business = null)
    {
        if (!is_null($business)) {
            $this->setBusiness($business);
        }
        if (sizeof(static::$allowParams) == 0) {
            static::$allowParams = array_merge(static::$required, static::$optional);
        }
    }

    /**
     * 设置商户.
     * 
     * @param Business $business
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setBusiness(Business $business)
    {
        if (!is_null($business)) {
            try {
                $business->checkParams();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
            $this->business = $business;
            $this->refundInfo = null;
        }

        return $this;
    }

    /**
     * 获取商户.
     * 
     * @return Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * 获取退款结果.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getResponse()
    {
        if (is_null($this->business)) {
            throw new Exception('Business is required');
        }

        static::$params['appid'] = $this->business->appid;
        static::$params['mch_id'] = $this->business->mch_id;
        $this->checkParams();
        $signGenerator = new SignGenerator(static::$params);
        $signGenerator->onSortAfter(function (SignGenerator $that) {
            $that->key = $this->business->mch_key;
        });
        static::$params['sign'] = $signGenerator->getResult();

        $request = XML::build(static::$params);
        //设置Http使用的证书
        $options['sslcert_path'] = $this->business->getClientCert();
        $options['sslkey_path'] = $this->business->getClientKey();

        $http = new Http();
        $response = $http->request(static::REFUNDORDER_URL, Http::POST, $request, $options);
        if (empty($response)) {
            throw new Exception('Get Refund Failure:');
        }
        $refundOrder = XML::parse($response);

        if (isset($refundOrder['return_code']) &&
            $refundOrder['return_code'] === 'FAIL') {
            throw new Exception($refundOrder['return_code'].': '.$refundOrder['return_msg']);
        }

        //返回签名数据校验
        if (empty($refundOrder) || empty($refundOrder['sign'])) {
            throw new Exception('param sign is missing or empty');
        }
        $sign = $refundOrder['sign'];
        unset($refundOrder['sign']);
        $signGenerator = new SignGenerator($refundOrder);
        $signGenerator->onSortAfter(function (SignGenerator $that) {
            $that->key = $this->business->mch_key;
        });
        if ($sign !== $signGenerator->getResult()) {
            throw new Exception('check sign error');
        }

        //返回结果判断
        if (isset($refundOrder['result_code']) &&
            ($refundOrder['result_code'] === 'FAIL')) {
            throw new Exception($refundOrder['err_code'].': '.$refundOrder['err_code_des']);
        }

        if (isset($refundOrder['return_code']) &&
            $refundOrder['return_code'] === 'FAIL') {
            throw new Exception($refundOrder['return_code'].': '.$refundOrder['return_msg']);
        }

        return $this->refundInfo = $refundOrder;
    }

    /**
     * 检测参数值是否有效.
     *
     * @throws Exception
     */
    public function checkParams()
    {
        foreach (static::$required as $paramName) {
            if (!array_key_exists($paramName, static::$params)) {
                throw new Exception(sprintf('"%s" is required', $paramName));
            }
        }

        if (!array_key_exists('transaction_id', static::$params) && !array_key_exists('out_trade_no', static::$params)) {
            throw new Exception('transaction_id or out_trade_no is required');
        }

        if (!array_key_exists('nonce_str', static::$params)) {
            static::$params['nonce_str'] = md5(uniqid(microtime()));
        }

        if (!array_key_exists('op_user_id', static::$params)) {
            static::$params['op_user_id'] = $this->business->mch_id;
        }
    }

    public function __set($property, $value)
    {
        if (!in_array($property, static::$allowParams)) {
            throw new Exception(sprintf('"%s" is not required', $property));
        }

        return static::$params[$property] = $value;
    }
}
