<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use JetBrains\PhpStorm\ArrayShape;

class Utils
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_1_4.shtml
     */
    #[ArrayShape([
        'appId' => "string",
        'timeStamp' => "string",
        'nonceStr' => "string",
        'package' => "string",
        'signType' => "string",
        'paySign' => "string"
    ])]
    public function buildBridgeConfig(string $prepayId, string $appId): array
    {
        $params = [
            'appId' => $appId,
            'timeStamp' => strval(time()),
            'nonceStr' => uniqid(),
            'package' => "prepay_id=$prepayId",
            'signType' => 'MD5',
        ];

        $params['paySign'] = $this->createSignature($params, $this->merchant->getSecretKey(), 'md5');

        return $params;
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_1_4.shtml
     */
    public function buildSdkConfig(string $prepayId, string $appId): array
    {
        $config = $this->buildBridgeConfig($prepayId, $appId);

        $config['timestamp'] = $config['timeStamp'];
        unset($config['timeStamp']);

        return $config;
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_2_4.shtml
     */
    #[ArrayShape([
        'appid' => "string",
        'partnerid' => "int",
        'prepayid' => "string",
        'noncestr' => "string",
        'timestamp' => "int",
        'package' => "string",
        'sign' => "string"
    ])]
    public function buildAppConfig(string $prepayId, string $appId): array
    {
        $params = [
            'appid' => $appId,
            'partnerid' => $this->merchant->getMerchantId(),
            'prepayid' => $prepayId,
            'noncestr' => uniqid(),
            'timestamp' => time(),
            'package' => 'Sign=WXPay',
        ];

        $params['sign'] = $this->createSignature($params, $this->merchant->getSecretKey());

        return $params;
    }

    protected function createSignature($attributes, $key, $algorithm = 'md5'): string
    {
        ksort($attributes);

        $attributes['key'] = $key;

        return strtoupper(call_user_func_array($algorithm, [urldecode(http_build_query($attributes))]));
    }
}
