<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Pay\Merchant;
use EasyWeChat\Tests\TestCase;

class MerchantTest extends TestCase
{
    public function test_construct()
    {
        $privateKey = new PrivateKey('mock-private-key');
        $publicKey = \Mockery::mock(PublicKey::class);

        $merchant = new Merchant(
            mchId: 100001,
            privateKey: $privateKey,
            certificate: $publicKey,
            secretKey: 'v3SecretKey',
            v2SecretKey: 'v2SecretKey',
        );

        $this->assertSame(100001, $merchant->getMerchantId());
        $this->assertSame($privateKey, $merchant->getPrivateKey());
        $this->assertSame('v3SecretKey', $merchant->getSecretKey());
        $this->assertSame('v2SecretKey', $merchant->getV2SecretKey());
        $this->assertSame($publicKey, $merchant->getCertificate());
    }
}
