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
 * Payment.php.
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
 * @link      http://blog.lost-magic.com
 * @link      https://github.com/thenbsp/Wechat
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Payment\UnifiedOrder;
use Overtrue\Wechat\Utils\JSON;
use Overtrue\Wechat\Utils\SignGenerator;

/**
 * 微信支付.
 */
class Payment
{
    /**
     * 统一下单.
     * 
     * @var UnifiedOrder
     */
    protected $unifiedOrder;

    public function __construct(UnifiedOrder $unifiedOrder)
    {
        $this->unifiedOrder = $unifiedOrder;
    }

    /**
     * 获取配置文件.
     * @param string $trade_type
     * @param bool $asJson
     * @return array|mixed
     * @throws Exception
     */
    public function getConfig($trade_type = 'JSAPI', $asJson = true)
    {
        if($trade_type=='JSAPI'){
            $config = $this->generateConfig();
        }elseif($trade_type=='APP'){
            $config = $this->generatePhoneConfig();
        }else{
            throw new Exception('不支持trade_type为'.$trade_type.'支付参数');
        }

        return $asJson ? JSON::encode($config) : $config;
    }

    /**
     * 获取配置文件（用于 Jssdk chooseWXPay 方式）.
     * 
     * @param bool|true $asJson
     *
     * @return array|string
     */
    public function getConfigJssdk($asJson = true)
    {
        $config = $this->generateConfig();
        $params = array(
            'timestamp' => $config['timeStamp'],
            'nonceStr' => $config['nonceStr'],
            'package' => $config['package'],
            'signType' => $config['signType'],
            'paySign' => $config['paySign'],
        );

        return $asJson ? JSON::encode($params) : $params;
    }

    /**
     * 生成配置.
     * 
     * @return array
     *
     * @throws Payment\Exception
     */
    private function generateConfig()
    {
        $response = $this->unifiedOrder->getResponse();
        $business = $this->unifiedOrder->getBusiness();
        $config = array(
            'appId' => $business->appid,
            'timeStamp' => (string) time(),
            'nonceStr' => $response['nonce_str'],
            'package' => 'prepay_id='.$response['prepay_id'],
            'signType' => 'MD5',
        );

        $signGenerator = new SignGenerator($config);
        $signGenerator->onSortAfter(function (SignGenerator $that) use ($business) {
            $that->key = $business->mch_key;
        });
        $config['paySign'] = $signGenerator->getResult();

        return $config;
    }

    /**
     * 获得移动端支付参数
     * @return array
     * @throws Payment\Exception
     */
    private function generatePhoneConfig(){
        $response = $this->unifiedOrder->getResponse();
        $business = $this->unifiedOrder->getBusiness();
        $config = array(
            'appId'         => $business->appid,
            'partnerId'     => $business->mch_id,
            'prepayId'      => $response['prepay_id'],
            'packageValue'  => 'Sign=WXPay',
            'nonceStr'      => $response['nonce_str'],
            'timeStamp'     => (string) time(),
        );
        $signGenerator = new SignGenerator($config);
        $signGenerator->onSortAfter(function (SignGenerator $that) use ($business) {
            $that->key = $business->mch_key;
        });
        $config['sign'] = $signGenerator->getResult();
        return $config;
    }
}
