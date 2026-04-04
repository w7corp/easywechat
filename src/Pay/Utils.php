<?php

namespace EasyWeChat\Pay;

use const OPENSSL_PKCS1_OAEP_PADDING;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\Str;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use EasyWeChat\Pay\Exceptions\EncryptionFailureException;
use EasyWeChat\Pay\Exceptions\SignatureFailureException;
use JetBrains\PhpStorm\ArrayShape;

use function base64_encode;
use function http_build_query;
use function is_string;
use function md5;
use function openssl_pkey_get_public;
use function strtoupper;
use function time;
use function urldecode;

class Utils
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SignatureFailureException
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
     * @throws SignatureFailureException
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
     * @throws SignatureFailureException
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
     * @return array<string, mixed>
     *
     * @throws SignatureFailureException
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

    /**
     * @throws SignatureFailureException
     */
    protected function createSignature(string $message): string
    {
        return (new Signature($this->merchant))->sign($message);
    }

    /**
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013053257
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013059044
     *
     * @param  string  $plaintext  The text to be encrypted.
     * @param  string|null  $serial  The serial number of the platform certificate to use for encryption. If null, the first available certificate will be used.
     * @return string The base64-encoded encrypted text.
     *
     * @throws InvalidConfigException If no platform certificate is found.
     * @throws EncryptionFailureException If the encryption process fails.
     */
    public function encryptWithRsaPublicKey(string $plaintext, ?string $serial = null): string
    {
        $platformCerts = $this->merchant->getPlatformCerts();
        $identifier = $serial ?? array_key_first($platformCerts);

        if (! is_string($identifier) || $identifier === '') {
            throw new InvalidConfigException('Missing platform certificate.');
        }

        $platformCert = $this->merchant->getPlatformCert($identifier);

        if (empty($platformCert)) {
            throw new InvalidConfigException('Missing platform certificate.');
        }

        $publicKey = openssl_pkey_get_public((string) $platformCert);

        if ($publicKey === false || ! openssl_public_encrypt($plaintext, $encrypted, $publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new EncryptionFailureException('Encrypt failed.');
        }

        return base64_encode($encrypted);
    }

    /**
     * @throws InvalidConfigException
     */
    public function createV2Signature(array $params): string
    {
        $secretKey = $this->merchant->getV2SecretKey();

        if (empty($secretKey)) {
            throw new InvalidConfigException('Missing v2 secret key.');
        }

        ksort($params);

        $signType = is_string($params['signType'] ?? null) ? $params['signType'] : 'MD5';
        $params['signType'] = $signType;
        $params['key'] = $secretKey;

        $message = urldecode(http_build_query($params));

        if ($signType === 'HMAC-SHA256') {
            $signature = hash_hmac('sha256', $message, $secretKey);
        } else {
            $signature = md5($message);
        }

        return strtoupper($signature);
    }
}
