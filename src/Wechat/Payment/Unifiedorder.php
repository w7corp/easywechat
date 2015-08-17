<?php
/**
 * Unifiedorder.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Frye <frye0423@gmail.com>
 * @copyright 2015 Frye <frye0423@gmail.com>
 * @link      https://github.com/0i
 * @link      https://github.com/thenbsp/Wechat
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Payment;
use Overtrue\Wechat\Utils\Util;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Http;
use Overtrue\Wechat\AccessToken;

class Unifiedorder
{
    /**
     * 统一下单接口
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1
     */
    const UNIFIEDORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    
    /**
     * @var Order   订单信息
     */
    protected $order;

    /**
     * @var Business    商户信息
     */
    protected $business;

    /**
     * @var Array   Unifiedorder缓存
     */
    protected $unifiedorder = null;
    
    public function __construct(Business $business = null, Order $order = null)
    {
        if(!is_null($order)) {
            $this->setOrder($order);
        }
        
        if(!is_null($business)) {
            $this->setBusiness($business);
        }
    }

    /**
     * 设置订单
     * @param Order $order
     *
     * @return $this
     * @throws Exception
     */
    public function setOrder(Order $order)
    {
        if( !is_null($order) ) {
            try {
                $order->checkParams();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
            
            if( !$order->hasParams('nonce_str') ) {
                $order->nonce_str(Util::randomString());
            }
            
            if( !$order->hasParams('spbill_create_ip') ) {
                $order->spbill_create_ip(Util::clientIP());
            }
            
            if( !$order->hasParams('trade_type') ) {
                if( !$order->hasParams('openid') ) {
                    throw new Exception('openid is required');
                }
                $order->trade_type('JSAPI');
            }
            $this->order = $order;
            $this->unifiedorder = null;
        }
        return $this;
    }

    /**
     * 获取订单
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * 设置商户
     * @param Business $business
     *
     * @return $this
     * @throws Exception
     */
    public function setBusiness(Business $business)
    {
        if( !is_null($business) ) {
            try {
                $business->checkParams();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
            $this->business = $business;
            $this->unifiedorder = null;
        }
        return $this;
    }

    /**
     * 获取商户
     * @return Business
     */
    public function getBusiness()
    {
        return $this->business;
    }

    /**
     * 获取统一下单结果
     * @param bool|false $force 是否忽略缓存强制更新
     *
     * @return array
     * @throws Exception
     * @throws \Overtrue\Wechat\Exception
     */
    public function getResponse($force = false)
    {
        if( is_null($this->business) ) {
            throw new Exception('Business is required');
        }
        if( is_null($this->order) ) {
            throw new Exception('Order is required');
        }
        if ($this->unifiedorder !== null && $force === false) {
            return $this->unifiedorder;
        }
        
        $params = $this->order->getParams();
        $params['appid']    = $this->business->getParams('appid');
        $params['mch_id']   = $this->business->getParams('mch_id');
        ksort($params);
        $sign = http_build_query($params);
        $sign = urldecode($sign).'&key='.$this->business->getParams('mch_key');
        $sign = strtoupper(md5($sign));
        $params['sign'] = $sign;
        $request = XML::build($params);
        
        $http = new Http(new AccessToken($this->business->getParams('appid'), $this->business->getParams('appsecret')));
        
        $response = $http->request(static::UNIFIEDORDER_URL, Http::POST, $request);
        if(empty($response)) {
            throw new Exception('Get Unifiedorder Failure:');
        }

        $unifiedorder = XML::parse($response);
        if( isset($unifiedorder['result_code']) &&
            ($unifiedorder['result_code'] === 'FAIL') ) {
            throw new Exception($unifiedorder['err_code'].': '.$unifiedorder['err_code_des']);
        }
        
        if( isset($unifiedorder['return_code']) &&
            $unifiedorder['return_code'] === 'FAIL' ) {
            throw new Exception($unifiedorder['return_code'].': '.$unifiedorder['return_msg']);
        }
        return $this->unifiedorder = $unifiedorder;
    }
}
