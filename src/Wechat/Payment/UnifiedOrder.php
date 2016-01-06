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
 * UnifiedOrder.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Frye <frye0423@gmail.com>
 * @copyright 2015 Frye <frye0423@gmail.com>
 *
 * @link      https://github.com/0i
 * @link      https://github.com/thenbsp/Wechat
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Http;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Utils\XML;

class UnifiedOrder
{
    /**
     * 统一下单接口
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_1.
     */
    const UNIFIEDORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

    /**
     * 订单信息.
     *
     * @var Order
     */
    protected $order;

    /**
     * 商户信息.
     *
     * @var Business
     */
    protected $business;

    /**
     * UnifiedOrder缓存.
     *
     * @var array
     */
    protected $unifiedOrder = null;

    public function __construct(Business $business = null, Order $order = null)
    {
        if (!is_null($order)) {
            $this->setOrder($order);
        }

        if (!is_null($business)) {
            $this->setBusiness($business);
        }
    }

    /**
     * 设置订单.
     *
     * @param Order $order
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setOrder(Order $order)
    {
        if ($order) {
            try {
                $order->checkParams();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }

            if (!$order->nonce_str) {
                $order->nonce_str = md5(uniqid(microtime()));
            }

            if (!$order->spbill_create_ip) {
                $order->spbill_create_ip = empty($_SERVER['REMOTE_ADDR']) ? '0.0.0.0' : $_SERVER['REMOTE_ADDR'];
            }

            if (!$order->trade_type) {
                if (!$order->openid) {
                    throw new Exception('openid is required');
                }
                $order->trade_type = 'JSAPI';
            }
            $this->order = $order;
            $this->unifiedOrder = null;
        }

        return $this;
    }

    /**
     * 获取订单.
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
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
            $this->unifiedOrder = null;
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
     * 获取统一下单结果.
     *
     * @param bool|false $force 是否忽略缓存强制更新
     *
     * @return array
     *
     * @throws Exception
     * @throws \Overtrue\Wechat\Exception
     */
    public function getResponse($force = false)
    {
        if (is_null($this->business)) {
            throw new Exception('Business is required');
        }
        if (is_null($this->order)) {
            throw new Exception('Order is required');
        }
        if ($this->unifiedOrder !== null && $force === false) {
            return $this->unifiedOrder;
        }

        $params = $this->order->toArray();
        $params['appid'] = $this->business->appid;
        $params['mch_id'] = $this->business->mch_id;
        $signGenerator = new SignGenerator($params);
        $me = $this;
        $signGenerator->onSortAfter(function (SignGenerator $that) use ($me) {
            $that->key = $me->business->mch_key;
        });

        $params['sign'] = $signGenerator->getResult();
        $request = XML::build($params);

        $http = new Http();

        $response = $http->request(static::UNIFIEDORDER_URL, Http::POST, $request);
        if (empty($response)) {
            throw new Exception('Get UnifiedOrder Failure:');
        }

        $unifiedOrder = XML::parse($response);
        if (isset($unifiedOrder['result_code']) &&
            ($unifiedOrder['result_code'] === 'FAIL')) {
            throw new Exception($unifiedOrder['err_code'].': '.$unifiedOrder['err_code_des']);
        }

        if (isset($unifiedOrder['return_code']) &&
            $unifiedOrder['return_code'] === 'FAIL') {
            throw new Exception($unifiedOrder['return_code'].': '.$unifiedOrder['return_msg']);
        }

        return $this->unifiedOrder = $unifiedOrder;
    }
}
