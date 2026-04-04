<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use EasyWeChat\Pay\URLSchemeBuilder;
use EasyWeChat\Tests\TestCase;

class URLSchemeBuilderTest extends TestCase
{
    public function test_for_code_url_encodes_nested_query_string()
    {
        $merchant = \Mockery::mock(MerchantInterface::class);
        $builder = new URLSchemeBuilder($merchant);

        $codeUrl = 'https://wx.tenpay.com/cgi-bin/mmpayweb-bin/checkmweb?prepay_id=wx123&package=mock-package';
        $url = $builder->forCodeUrl($codeUrl);

        $this->assertSame('weixin://wxpay/bizpayurl', \sprintf(
            '%s://%s%s',
            \parse_url($url, PHP_URL_SCHEME),
            \parse_url($url, PHP_URL_HOST),
            \parse_url($url, PHP_URL_PATH)
        ));

        \parse_str((string) \parse_url($url, PHP_URL_QUERY), $query);

        $this->assertSame($codeUrl, $query['sr'] ?? null);
        $this->assertArrayNotHasKey('package', $query);
    }
}
