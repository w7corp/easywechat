<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use function call_user_func_array;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Support\Str;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use function hash_hmac;
use function http_build_query;
use function is_string;
use function strtoupper;
use function urldecode;

class LegacySignature
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws \Exception
     */
    public function sign(array $params): array
    {
        $nonce = Str::random();

        $params = $attributes = array_filter(
            \array_merge(
                [
                    'nonce_str' => $nonce,
                    'sub_mch_id' => $params['sub_mch_id'] ?? null,
                    'sub_appid' => $params['sub_appid'] ?? null,
                ],
                $params
            )
        );

        ksort($attributes);

        $attributes['key'] = $this->merchant->getV2SecretKey();

        if (empty($attributes['key'])) {
            throw new InvalidConfigException('Missing V2 API key.');
        }

        if (! empty($params['sign_type']) && 'HMAC-SHA256' === $params['sign_type']) {
            $signType = fn (string $message): string => hash_hmac('sha256', $message, $attributes['key']);
        } else {
            $signType = 'md5';
        }

        $sign = call_user_func_array($signType, [urldecode(http_build_query($attributes))]);

        if (! is_string($sign)) {
            throw new RuntimeException('Failed to sign the request.');
        }

        $params['sign'] = strtoupper($sign);

        return $params;
    }
}
