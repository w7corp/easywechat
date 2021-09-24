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
        'appId'     => "string",
        'timeStamp' => "string",
        'nonceStr'  => "string",
        'package'   => "string",
        'signType'  => "string",
        'paySign'   => "string",
    ])]
    public function buildBridgeConfig(string $prepayId, string $appId): array
    {
        $params = [
            'appId'     => $appId,
            'timeStamp' => strval(time()),
            'nonceStr'  => uniqid(),
            'package'   => "prepay_id=$prepayId",
            'signType'  => 'RSA',
        ];

        $message = $params['appId'] . "\n" .
            $params['timeStamp'] . "\n" .
            $params['nonceStr'] . "\n" .
            $params['package'] . "\n";

        $params['paySign'] = $this->createSignature($message);

        return $params;
    }

    /**
     * @see https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#58
     */
    #[ArrayShape([
        'appId'     => "string",
        'nonceStr'  => "string",
        'package'   => "string",
        'signType'  => "string",
        'paySign'   => "string",
        'timestamp' => "string",
    ])]
    public function buildSdkConfig(string $prepayId, string $appId): array
    {
        $params = $this->buildBridgeConfig($prepayId, $appId);

        $params['timestamp'] = $params['timeStamp'];
        unset($params['timeStamp']);

        return $params;
    }

    /**
     * @see https://developers.weixin.qq.com/miniprogram/dev/api/payment/wx.requestPayment.html
     */
    #[ArrayShape([
        'appId'     => "string",
        'timeStamp' => "string",
        'nonceStr'  => "string",
        'package'   => "string",
        'signType'  => "string",
        'paySign'   => "string",
    ])]
    public function buildMiniAppConfig(string $prepayId, string $appId): array
    {
        return $this->buildBridgeConfig($prepayId, $appId);
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_2_4.shtml
     */
    #[ArrayShape([
        'appid'     => "string",
        'partnerid' => "int",
        'prepayid'  => "string",
        'noncestr'  => "string",
        'timestamp' => "int",
        'package'   => "string",
        'sign'      => "string",
    ])]
    public function buildAppConfig(string $prepayId, string $appId): array
    {
        $params = [
            'appid'     => $appId,
            'partnerid' => $this->merchant->getMerchantId(),
            'prepayid'  => $prepayId,
            'noncestr'  => uniqid(),
            'timestamp' => time(),
            'package'   => 'Sign=WXPay',
        ];

        $message = $params['appId'] . "\n" .
            $params['timeStamp'] . "\n" .
            $params['nonceStr'] . "\n" .
            $params['prepayid'] . "\n";

        $params['sign'] = $this->createSignature($message);

        return $params;
    }

    protected function createSignature(string $message): string
    {
        \openssl_sign($message, $signature, $this->merchant->getPrivateKey(), 'sha256WithRSAEncryption');

        return \base64_encode($signature);
    }
}
