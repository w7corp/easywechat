<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Support\Str;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use JetBrains\PhpStorm\ArrayShape;

class Utils
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_1_4.shtml
     * @return array<string, mixed>
     * @throws \Exception
     */
    #[ArrayShape([
        'appId' => "string",
        'timeStamp' => "string",
        'nonceStr' => "string",
        'package' => "string",
        'signType' => "string",
        'paySign' => "string",
    ])]
    public function buildBridgeConfig(string $prepayId, string $appId, string $signType = 'RSA'): array
    {
        $params = [
            'appId' => $appId,
            'timeStamp' => strval(time()),
            'nonceStr' => Str::random(),
            'package' => "prepay_id=$prepayId",
            'signType' => $signType,
        ];

        $message = $params['appId'] . "\n" .
            $params['timeStamp'] . "\n" .
            $params['nonceStr'] . "\n" .
            $params['package'] . "\n";

        $params['paySign'] = $this->createSignature($message);

        if ($signType != 'RSA') {
            $signMethod = $this->get_encrypt_method($signType, $this->merchant->getV2SecretKey());
            $params['paySign'] = $this->generate_sign($params, $this->merchant->getV2SecretKey(), $signMethod);
        }

        return $params;
    }

    /**
     * @see https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#58
     * @return array<string, mixed>
     * @throws \Exception
     */
    #[ArrayShape([
        'appId' => "string",
        'nonceStr' => "string",
        'package' => "string",
        'signType' => "string",
        'paySign' => "string",
        'timestamp' => "string",
    ])]
    public function buildSdkConfig(string $prepayId, string $appId, string $signType = 'RSA'): array
    {
        $params = $this->buildBridgeConfig($prepayId, $appId, $signType);

        $params['timestamp'] = $params['timeStamp'];
        unset($params['timeStamp']);

        return $params;
    }

    /**
     * @see https://developers.weixin.qq.com/miniprogram/dev/api/payment/wx.requestPayment.html
     * @return array<string, mixed>
     * @throws \Exception
     */
    #[ArrayShape([
        'appId' => "string",
        'timeStamp' => "string",
        'nonceStr' => "string",
        'package' => "string",
        'signType' => "string",
        'paySign' => "string",
    ])]
    public function buildMiniAppConfig(string $prepayId, string $appId, string $signType = 'RSA'): array
    {
        return $this->buildBridgeConfig($prepayId, $appId, $signType);
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_2_4.shtml
     * @return array<string, mixed>
     * @throws \Exception
     */
    #[ArrayShape([
        'appid' => "string",
        'partnerid' => "int",
        'prepayid' => "string",
        'noncestr' => "string",
        'timestamp' => "int",
        'package' => "string",
        'sign' => "string",
    ])]
    public function buildAppConfig(string $prepayId, string $appId): array
    {
        $params = [
            'appid' => $appId,
            'partnerid' => $this->merchant->getMerchantId(),
            'prepayid' => $prepayId,
            'noncestr' => Str::random(),
            'timestamp' => \time(),
            'package' => 'Sign=WXPay',
        ];

        $message = $params['appid'] . "\n" .
            $params['timestamp'] . "\n" .
            $params['noncestr'] . "\n" .
            $params['prepayid'] . "\n";

        $params['sign'] = $this->createSignature($message);

        return $params;
    }

    protected function createSignature(string $message): string
    {
        \openssl_sign($message, $signature, $this->merchant->getPrivateKey(), 'sha256WithRSAEncryption');

        return \base64_encode($signature);
    }

    /**
     * Generate a signature.
     *
     * @param array  $attributes
     * @param string $key
     * @param string $encryptMethod
     *
     * @return string
     */
    public function generate_sign(array $attributes, string $key, $encryptMethod = 'md5'): string
    {
        ksort($attributes);

        $attributes['key'] = $key;

        return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($attributes))]));
    }

    /**
     * @param string $signType
     * @param string $secretKey
     *
     * @return \Closure|string
     */
    public function get_encrypt_method(string $signType, string $secretKey = ''): \Closure | string
    {
        if ('HMAC-SHA256' === $signType) {
            return function ($str) use ($secretKey) {
                return hash_hmac('sha256', $str, $secretKey);
            };
        }

        return 'md5';
    }
}
