<?php

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Pay\Merchant;
use EasyWeChat\Tests\TestCase;

class MerchantTest extends TestCase
{
    public function test_construct()
    {
        $merchant = new Merchant(
            mchId: 100001,
            privateKey: '----mock-key----',
            secretKey: 'fjpfz2fdrze4kfcb3mfdipmnlx5t1111',
            certificate: '----mock-certificate----',
            certificateSerialNo: '30391CF991DE2548C1FF15D42B97012D12345678',
        );

        $this->assertSame(100001, $merchant->getMerchantId());
        $this->assertSame('----mock-key----', $merchant->getPrivateKey());
        $this->assertSame('fjpfz2fdrze4kfcb3mfdipmnlx5t1111', $merchant->getSecretKey());
        $this->assertSame('----mock-certificate----', $merchant->getCertificate());
        $this->assertSame('30391CF991DE2548C1FF15D42B97012D12345678', $merchant->getCertificateSerialNumber());
    }

    public function test_load_key_and_cert_from_path()
    {
        $merchant = new Merchant(
            mchId: 100001,
            privateKey: __DIR__.'/../stubs/files/demo_key.pem',
            secretKey: 'fjpfz2fdrze4kfcb3mfdipmnlx5t1111',
            certificate: __DIR__.'/../stubs/files/demo_cert.pem',
            certificateSerialNo: '30391CF991DE2548C1FF15D42B97012D12345678',
        );

        $this->assertSame(\file_get_contents(__DIR__.'/../stubs/files/demo_key.pem'), $merchant->getPrivateKey());
        $this->assertSame(\file_get_contents(__DIR__.'/../stubs/files/demo_cert.pem'), $merchant->getCertificate());
    }
}
