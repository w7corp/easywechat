<?php

namespace EasyWeChat\Pay;

use function base64_encode;
use function call_user_func_array;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\Str;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Exception;
use function http_build_query;
use JetBrains\PhpStorm\ArrayShape;
use function openssl_sign;
use function strtoupper;
use function time;
use function urldecode;

class Utils
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_1_4.shtml
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    #[ArrayShape([
        'appId' => 'string',
        'timeStamp' => 'string',
        'nonceStr' => 'string',
        'package' => 'string',
        'signType' => 'string',
        'paySign' => 'string',
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

        $message = $params['appId']."\n".
            $params['timeStamp']."\n".
            $params['nonceStr']."\n".
            $params['package']."\n";

        // v2
        if ($signType != 'RSA') {
            $params['paySign'] = $this->createV2Signature($params);
        } else {
            // v3
            $params['paySign'] = $this->createSignature($message);
        }

        return $params;
    }

    /**
     * @see https://developers.weixin.qq.com/doc/offiaccount/OA_Web_Apps/JS-SDK.html#58
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    #[ArrayShape([
        'appId' => 'string',
        'nonceStr' => 'string',
        'package' => 'string',
        'signType' => 'string',
        'paySign' => 'string',
        'timestamp' => 'string',
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
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    #[ArrayShape([
        'appId' => 'string',
        'timeStamp' => 'string',
        'nonceStr' => 'string',
        'package' => 'string',
        'signType' => 'string',
        'paySign' => 'string',
    ])]
    public function buildMiniAppConfig(string $prepayId, string $appId, string $signType = 'RSA'): array
    {
        return $this->buildBridgeConfig($prepayId, $appId, $signType);
    }

    /**
     * @see https://pay.weixin.qq.com/wiki/doc/apiv3_partner/apis/chapter4_2_4.shtml
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    #[ArrayShape([
        'appid' => 'string',
        'partnerid' => 'int',
        'prepayid' => 'string',
        'noncestr' => 'string',
        'timestamp' => 'int',
        'package' => 'string',
        'sign' => 'string',
    ])]
    public function buildAppConfig(string $prepayId, string $appId): array
    {
        $params = [
            'appid' => $appId,
            'partnerid' => $this->merchant->getMerchantId(),
            'prepayid' => $prepayId,
            'noncestr' => Str::random(),
            'timestamp' => time(),
            'package' => 'Sign=WXPay',
        ];

        $message = $params['appid']."\n".
            $params['timestamp']."\n".
            $params['noncestr']."\n".
            $params['prepayid']."\n";

        $params['sign'] = $this->createSignature($message);

        return $params;
    }

    protected function createSignature(string $message): string
    {
        openssl_sign($message, $signature, $this->merchant->getPrivateKey(), 'sha256WithRSAEncryption');

        return base64_encode($signature);
    }

    /**
     * @throws InvalidConfigException
     */
    public function createV2Signature(array $params): string
    {
        $method = 'md5';
        $secretKey = $this->merchant->getV2SecretKey();

        if (empty($secretKey)) {
            throw new InvalidConfigException('Missing v2 secret key.');
        }

        if ('HMAC-SHA256' === $params['signType']) {
            $method = function ($str) use ($secretKey) {
                return hash_hmac('sha256', $str, $secretKey);
            };
        }

        ksort($params);

        $params['key'] = $secretKey;

        // @phpstan-ignore-next-line
        return strtoupper((string) call_user_func_array($method, [urldecode(http_build_query($params))]));
    }
}
