<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;

class LegacySignature
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    public function sign(array $params): array
    {
        $nonce = \uniqid('nonce');

        $params = $attributes = array_filter(
            \array_merge(
                [
                    'mch_id' => $this->merchant->getMerchantId(),
                    'nonce_str' => $nonce,
                    'sub_mch_id' => $params['sub_mch_id'] ?? null,
                    'sub_appid' => $params['sub_appid'] ?? null,
                ],
                $params
            )
        );

        ksort($attributes);

        $attributes['key'] = $this->merchant->getSecretKey();

        if (!empty($params['sign_type']) && 'HMAC-SHA256' === $params['sign_type']) {
            $signType = fn ($message) => hash_hmac('sha256', $message, $this->merchant->getSecretKey());
        } else {
            $signType = 'md5';
        }

        $params['sign'] = strtoupper(call_user_func_array($signType, [urldecode(http_build_query($params))]));

        return $params;
    }
}
