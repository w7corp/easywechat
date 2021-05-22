<?php

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Kernel\ApiBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

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
}
