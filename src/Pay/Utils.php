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
    public function buildBridgeConfig(string $prepayId, string $appId, $signType = 'RSA'): array
    {
        $params = [
            'appId'     => $appId,
            'timeStamp' => strval(time()),
            'nonceStr'  => uniqid(),
            'package'   => "prepay_id=$prepayId",
            'signType'  => strtoupper($signType),
        ];

        $params['paySign'] = $this->createSignature($params, $signType);

        return $params;
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
        'timestamp' => "string",
    ])]
    public function buildSdkConfig(string $prepayId, string $appId, $signType = 'RSA'): array
    {
        $config = $this->buildBridgeConfig($prepayId, $appId, $signType);

        $config['timestamp'] = $config['timeStamp'];
        unset($config['timeStamp']);

        return $config;
    }

    /**
     * @see https://developers.weixin.qq.com/miniprogram/dev/api/payment/wx.requestPayment.html
     */
    public function buildMiniAppConfig(string $prepayId, string $appId, $signType = 'RSA'): array
    {
        return $this->buildBridgeConfig($prepayId, $appId, $signType);
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
    public function buildAppConfig(string $prepayId, string $appId, $signType = 'RSA'): array
    {
        $params = [
            'appid'     => $appId,
            'partnerid' => $this->merchant->getMerchantId(),
            'prepayid'  => $prepayId,
            'noncestr'  => uniqid(),
            'timestamp' => time(),
            'package'   => 'Sign=WXPay',
        ];

        $params['sign'] = $this->createSignature($params, $signType);

        return $params;
    }

    protected function createSignature(array $attributes, $algorithm = 'RSA'): string
    {
        return call_user_func_array(
            [$this, 'create' . $algorithm . 'Signature'],
            [$attributes]
        );
    }

    protected function createRSASignature(array $attributes): string
    {
        $message = $attributes['appId'] . "\n" .
            $attributes['timeStamp'] . "\n" .
            $attributes['nonceStr'] . "\n" .
            $attributes['package'] . "\n";

        \openssl_sign($message, $signature, $this->merchant->getPrivateKey(), 'sha256WithRSAEncryption');

        return \base64_encode($signature);
    }

    protected function createMD5Signature(array $attributes): string
    {
        ksort($attributes);

        $attributes['key'] = $this->merchant->getSecretKey();

        return strtoupper(md5(urldecode(http_build_query($attributes))));
    }
}
