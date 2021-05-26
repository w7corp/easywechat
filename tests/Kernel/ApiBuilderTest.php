<?php

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Kernel\ApiBuilder;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiBuilderTest extends TestCase
{
    public function test_uri_appends()
    {
        // without basic uri
        $builer = new ApiBuilder(HttpClient::create());

        // basic
        $this->assertSame('/v3/pay/transactions/native', actual: $builer->v3->pay->transactions->native->getUri());

        // camel-case
        $this->assertSame('/v3/merchant-service', $builer->v3->merchantService->getUri());

        // variable
        $merchantId = 11000000;
        $this->assertSame(
            "/v3/combine-transactions/out-trade-no/{$merchantId}/close",
            $builer->v3->combineTransactions->outTradeNo->$merchantId->close->getUri()
        );


        // with basic uri
        $builer = new ApiBuilder(HttpClient::create(), 'v3/pay/');

        $this->assertSame('/v3/pay/transactions/native', actual: $builer->transactions->native->getUri());
    }

    public function test_full_uri_call()
    {
        $client = \Mockery::mock(HttpClientInterface::class);
        $builer = new ApiBuilder($client, 'v3');

        $client->expects()->request('GET', 'https://api2.mch.weixin.qq.com/v3/certificates', [])->once();
        $builer->get('https://api2.mch.weixin.qq.com/v3/certificates');


        $options = [
            'headers' => [
                'accept' => 'application/json'
            ]
        ];
        $client->expects()->request('GET', 'https://api2.mch.weixin.qq.com/v3/certificates', $options)->once();

        $builer->get('https://api2.mch.weixin.qq.com/v3/certificates', $options);
    }

    public function test_shortcuts_call()
    {
        $client = \Mockery::mock(HttpClientInterface::class);
        $builer = new ApiBuilder($client, 'v3');

        $client->expects()->request('GET', '/v3/certificates', [])->once();
        $builer->get('certificates');


        $options = [
            'headers' => [
                'accept' => 'application/json'
            ]
        ];
        $client->expects()->request('GET', '/v3/certificates', $options)->once();

        $builer->get('certificates', $options);
    }
}
