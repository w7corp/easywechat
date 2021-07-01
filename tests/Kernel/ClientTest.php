<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Kernel\Client;
use EasyWeChat\Tests\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ClientTest extends TestCase
{
    public function test_uri_appends()
    {
        // without basic uri
        $client = new Client();

        // basic
        $this->assertSame('v3/pay/transactions/native', actual: $client->v3->pay->transactions->native->getUri());

        // camel-case
        $this->assertSame('v3/merchant-service', $client->v3->merchantService->getUri());

        // variable
        $merchantId = 11000000;
        $this->assertSame(
            "v3/combine-transactions/out-trade-no/{$merchantId}/close",
            $client->v3->combineTransactions->outTradeNo->$merchantId->close->getUri()
        );

        // with basic uri
        $client = new Client(uri: 'v3/pay/');

        $this->assertSame('v3/pay/transactions/native', actual: $client->transactions->native->getUri());
    }

    public function test_full_uri_call()
    {
        $client = \Mockery::mock(HttpClientInterface::class);
        $client = new Client(client: $client, uri: 'v3');

        $client->expects()->request('GET', 'https://api2.mch.weixin.qq.com/v3/certificates', [])->once();
        $client->get('https://api2.mch.weixin.qq.com/v3/certificates');


        $options = [
            'headers' => [
                'accept' => 'application/json',
            ],
        ];
        $client->expects()->request('GET', 'https://api2.mch.weixin.qq.com/v3/certificates', $options)->once();

        $client->get('https://api2.mch.weixin.qq.com/v3/certificates', $options);
    }

    public function test_shortcuts_call()
    {
        $client = \Mockery::mock(HttpClientInterface::class);
        $client = new Client(client: $client, uri: 'v3');

        $client->expects()->request('GET', 'v3/certificates', [])->once();
        $client->get('certificates');


        $options = [
            'headers' => [
                'accept' => 'application/json',
            ],
        ];
        $client->expects()->request('GET', 'v3/certificates', $options)->once();

        $client->get('certificates', $options);
    }
}
