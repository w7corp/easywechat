<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;

class URLSchemeBuilder
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    public function forProduct(string|int $productId, string $appId): string
    {
        $params = [
            'appid' => $appId,
            'mch_id' => $this->merchant->getMerchantId(),
            'time_stamp' => time(),
            'nonce_str' => uniqid(),
            'product_id' => $productId,
        ];

        $params['sign'] = (new LegacySignature($this->merchant))->sign($params);

        return 'weixin://wxpay/bizpayurl?'.http_build_query($params);
    }

    public function forCodeUrl(string $codeUrl): string
    {
        return \sprintf('weixin://wxpay/bizpayurl?sr=%s', $codeUrl);
    }
}