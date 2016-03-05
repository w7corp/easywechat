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
 * QueryOrder.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    jaring <pengjiayin@gmail.com>
 */

namespace Overtrue\Wechat\Payment;

use Overtrue\Wechat\Http;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\SignGenerator;
use Overtrue\Wechat\Utils\XML;

class QueryOrder
{
    /**
     * 查询订单接口
     * https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_2.
     */
    const QUERYORDER_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';

    protected $appId;
    protected $appSecret;
    protected $mchId;
    protected $mchKey;

    /**
     * @var Bag
     */
    protected $transactionInfo;

    public function __construct($appId, $appSecret, $mchId, $mchKey)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->mchId = $mchId;
        $this->mchKey = $mchKey;
    }

    /**
     * 获取订单结果.
     *
     * @param string     $order_id 商户订单ID
     * @param bool|false $force    是否忽略缓存强制更新
     *
     * @return Bag
     *
     * @throws Exception
     * @throws \Overtrue\Wechat\Exception
     */
    public function getTransaction($order_id, $force = false)
    {
        $params = array();
        $params['appid'] = $this->appId;
        $params['mch_id'] = $this->mchId;
        $params['out_trade_no'] = $order_id;
        $params['nonce_str'] = md5(uniqid(microtime()));
        $signGenerator = new SignGenerator($params);
        $signGenerator->onSortAfter(function (SignGenerator $that) {
            $that->key = $this->mchKey;
        });
        $params['sign'] = $signGenerator->getResult();
        $request = XML::build($params);
        $http = new Http();
        $response = $http->request(static::QUERYORDER_URL, Http::POST, $request);
        if (empty($response)) {
            throw new Exception('Get ORDER Failure:');
        }

        $transaction = XML::parse($response);
        //返回签名数据校验
        if (empty($transaction) || empty($transaction['sign'])) {
            return false;
        }
        $sign = $transaction['sign'];
        unset($transaction['sign']);
        $signGenerator = new SignGenerator($transaction);
        $signGenerator->onSortAfter(function (SignGenerator $that) {
            $that->key = $this->mchKey;
        });
        if ($sign !== $signGenerator->getResult()) {
            return false;
        }

        // 返回结果判断
        if (isset($transaction['result_code']) &&
            ($transaction['result_code'] === 'FAIL')) {
            throw new Exception($transaction['err_code'].': '.$transaction['err_code_des']);
        }

        if (isset($transaction['return_code']) &&
            $transaction['return_code'] === 'FAIL') {
            throw new Exception($transaction['return_code'].': '.$transaction['return_msg']);
        }

        return $transactionInfo = new Bag($transaction);
    }
}
