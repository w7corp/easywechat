<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Client;
use EasyWeChat\Payment\Order;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSchema()
    {
        $app = new Application();

        $mock = $this->mockApiClient(Client::class, 'scheme', $app)->makePartial();

        $productId = '1';

        $this->assertNotEmpty($mock->scheme($productId));
        $this->assertStringStartsWith('weixin://wxpay/bizpayurl?', $mock->scheme($productId));
    }

    public function testPay()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['pay', 'wrapApi'], $app)->makePartial();

        // mock order
        $order = \Mockery::mock(Order::class.'[all]')->shouldDeferMissing();

        $client->expects()->request($client->wrapApi('pay/micropay'), $order->all())->andReturn('mock-result');
        $this->assertSame('mock-result', $client->pay($order));
    }

    public function testPrepare()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-merchant-id',
        ]);

        $client = $this->mockApiClient(Client::class, ['prepare', 'request'], $app)->makePartial();

        $order = \Mockery::mock(Order::class.'[all, get, set]')->makePartial();

        $order->set('notify_url', 'https://easywechat.org');

        // $order->spbill_create_ip is null and trade_type === Order::NATIVE
        $order->set('trade_type', Order::NATIVE);
        $client->expects()->request($client->wrapApi('pay/unifiedorder'), array_merge($order->all(), ['spbill_create_ip' => Support\get_server_ip()]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->prepare($order));

        // $order->spbill_create_ip is null and trade_type !== Order::NATIVE
        $order->set('trade_type', Order::JSAPI);
        $client->expects()->request($client->wrapApi('pay/unifiedorder'), array_merge($order->all(), ['spbill_create_ip' => Support\get_client_ip()]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->prepare($order));

        // $order->spbill_create_ip is not null.
        $order->set('spbill_create_ip', '192.168.0.1');
        $client->expects()->request($client->wrapApi('pay/unifiedorder'), $order->all())->andReturn('mock-result');

        $this->assertSame('mock-result', $client->prepare($order));
    }

    public function testQuery()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, 'query', $app)->makePartial();

        $orderNo = 'foo';
        $type = 'bar';

        // default type
        $client->expects()->request($client->wrapApi('pay/orderquery'), [Client::OUT_TRADE_NO => $orderNo])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->query($orderNo));

        // pass a type parameter
        $client->expects()->request($client->wrapApi('pay/orderquery'), [$type => $orderNo])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->query($orderNo, $type));
    }

    public function testQueryByTransactionId()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['query', 'queryByTransactionId'], $app)->makePartial();

        $transactionId = 'foo';

        $client->expects()->query($transactionId, Client::TRANSACTION_ID)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->queryByTransactionId($transactionId));
    }

    public function testClose()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, 'close', $app)->makePartial();

        $tradeNo = 'foo';

        $client->expects()->request($client->wrapApi('pay/closeorder'), ['out_trade_no' => $tradeNo])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->close($tradeNo));
    }

    public function testReverse()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['reverse', 'safeRequest'], $app)->makePartial();

        $orderNo = 'foo';

        // default type === 'out_trade_no'
        $client->expects()->safeRequest($client->wrapApi('secapi/pay/reverse'), [Client::OUT_TRADE_NO => $orderNo])->andReturn('mock-result-out-trade-no');
        $this->assertSame('mock-result-out-trade-no', $client->reverse($orderNo));

        // pass a type parameter
        $type = Client::TRANSACTION_ID;
        $client->expects()->safeRequest($client->wrapApi('secapi/pay/reverse'), [$type => $orderNo])->andReturn('mock-result-with-type');
        $this->assertSame('mock-result-with-type', $client->reverse($orderNo, $type));
    }

    public function testReverseByTransactionId()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['reverse', 'reverseByTransactionId'], $app)->makePartial();

        $transactionId = 'foo';

        $client->expects()->reverse($transactionId, Client::TRANSACTION_ID)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->reverseByTransactionId($transactionId));
    }

    public function testRefund()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-merchant-id',
        ]);

        $client = $this->mockApiClient(Client::class, ['refund', 'safeRequest'], $app)->makePartial();

        $orderNo = 'foo';
        $refundNo = 'bar';
        $totalFee = 1;
        $optional = ['foo' => 'bar'];

        $params = array_merge([
            Client::TRANSACTION_ID => $orderNo,
            'out_refund_no' => $refundNo,
            'total_fee' => $totalFee,
            'refund_fee' => $totalFee,
            'refund_fee_type' => $app['merchant']->fee_type,
            'refund_account' => 'REFUND_SOURCE_UNSETTLED_FUNDS',
            'op_user_id' => $app['merchant']->merchant_id,
        ], $optional);

        $client->expects()->safeRequest($client->wrapApi('secapi/pay/refund'), $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->refund($orderNo, $refundNo, $totalFee, $optional));
    }

    public function testRefundByTransactionId()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['refund', 'refundByTransactionId'], $app)->makePartial();

        $orderNo = 'foo';
        $refundNo = 'bar';
        $totalFee = 1;
        $optional = [];

        $client->expects()->refund($orderNo, $refundNo, $totalFee, $optional)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->refundByTransactionId($orderNo, $refundNo, $totalFee, $optional));
    }

    public function testQueryRefund()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, 'queryRefund', $app)->makePartial();

        $orderNo = 'foo';
        $type = 'bar';

        // default type
        $client->expects()->request($client->wrapApi('pay/refundquery'), [Client::OUT_TRADE_NO => $orderNo])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryRefund($orderNo));

        // pass a type parameter
        $client->expects()->request($client->wrapApi('pay/refundquery'), [$type => $orderNo])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryRefund($orderNo, $type));
    }

    public function testQueryRefundByRefundNo()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['queryRefund', 'queryRefundByRefundNo'], $app)->makePartial();

        $refundNo = 'foo';

        $client->expects()->queryRefund($refundNo, Client::OUT_REFUND_NO)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryRefundByRefundNo($refundNo));
    }

    public function testQueryRefundByTransactionId()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['queryRefund', 'queryRefundByTransactionId'], $app)->makePartial();

        $transactionId = 'foo';

        $client->expects()->queryRefund($transactionId, Client::TRANSACTION_ID)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryRefundByTransactionId($transactionId));
    }

    public function testQueryRefundByRefundId()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['queryRefund', 'queryRefundByRefundId'], $app)->makePartial();

        $refundId = 'foo';

        $client->expects()->queryRefund($refundId, Client::REFUND_ID)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryRefundByRefundId($refundId));
    }

    public function testDownloadBill()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, ['downloadBill', 'getBody'], $app)->makePartial();

        $data = 'foo';

        $client->shouldReceive('request->getBody')
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->downloadBill($data));
    }

    public function testReport()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, 'report', $app)->makePartial();

        $api = 'foo';
        $timeConsuming = 1;
        $resultCode = 'bar';
        $returnCode = 'baz';
        $optional = [];

        $params = array_merge([
            'interface_url' => $api,
            'execute_time_' => $timeConsuming,
            'return_code' => $returnCode,
            'return_msg' => null,
            'result_code' => $resultCode,
            'user_ip' => Support\get_client_ip(),
            'time' => time(),
        ], $optional);

        $client->expects()->request($client->wrapApi('payitil/report'), $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->report($api, $timeConsuming, $resultCode, $returnCode, $optional));
    }

    public function testAuthCodeToOpenId()
    {
        $app = new Application();

        $client = $this->mockApiClient(Client::class, 'authCodeToOpenId', $app)->makePartial();

        $authCode = 'foo';

        $client->expects()->request('https://api.mch.weixin.qq.com/tools/authcodetoopenid', ['auth_code' => $authCode])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->authCodeToOpenId($authCode));
    }

    public function testPrepends()
    {
        $app = new Application([
            'app_id' => '123456',
            'merchant_id' => 'foo-merchant-id',
            'key' => 'key123456',
        ]);

        $mock = $this->mockApiClient(Client::class, 'prepends', $app)->makePartial();

        $this->assertNotEmpty($mock->prepends());
        $this->assertArrayHasKey('appid', $mock->prepends());
    }
}
