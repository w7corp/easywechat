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

use EasyWeChat\Core\Http;
use EasyWeChat\Payment\API;
use EasyWeChat\Payment\Merchant;
use EasyWeChat\Payment\Order;
use EasyWeChat\Support\XML;
use EasyWeChat\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;

class PaymentAPITest extends TestCase
{
    /**
     * Build API instance.
     *
     * @return API
     */
    public function getAPI($sandboxEnabled = false)
    {
        $http = \Mockery::mock(Http::class);

        $http->shouldReceive('request')->andReturnUsing(function ($api, $method, $options) {
            $params = XML::parse($options['body']);

            return XML::build(compact('api', 'params'));
        });

        $merchant = new Merchant([
                'fee_type' => 'CNY',
                'merchant_id' => 'testMerchantId',
                'app_id' => 'wxTestAppId',
                'device_info' => 'testDeviceInfo',
                'key' => 'testKey',
                'notify_url' => 'merchant_default_notify_url',
            ]);

        $api = \Mockery::mock('EasyWeChat\Payment\API[getHttp,getCache]', [$merchant])
                 ->shouldAllowMockingProtectedMethods();

        $api->shouldReceive('wrapApi')->passthru();
        $api->shouldReceive('getHttp')->andReturn($http);

        if ($sandboxEnabled) {
            $api->shouldReceive('getCache')->andReturn($this->getMockCache());
        }

        return $api->sandboxMode($sandboxEnabled);
    }

    public function getMockCache()
    {
        $cache = \Mockery::mock(\Doctrine\Common\Cache\Cache::class);
        $cache->shouldReceive('fetch')->with('sandbox_signkey.testMerchantId')->andReturn('sandbox-signkey');

        return $cache;
    }

    /**
     * Test prepare().
     */
    public function testPrepare()
    {
        $api = $this->getAPI();

        $order = new Order(['foo' => 'bar']);
        $order->shouldReceive('all')->andReturn(['foo' => 'bar']);

        $response = $api->prepare($order);

        $this->assertSame($api->wrapApi(API::API_PREPARE_ORDER), $response['api']);
        $this->assertSame('wxTestAppId', $response['params']['appid']);
        $this->assertSame('merchant_default_notify_url', $response['params']['notify_url']);
        $this->assertSame('testMerchantId', $response['params']['mch_id']);
        $this->assertSame('bar', $response['params']['foo']);
    }

    /**
     * Test pay().
     */
    public function testPay()
    {
        $api = $this->getAPI();

        $order = new Order(['foo' => 'bar']);
        $order->shouldReceive('all')->andReturn(['foo' => 'bar']);

        $response = $api->pay($order);

        $this->assertSame($api->wrapApi(API::API_PAY_ORDER), $response['api']);
        $this->assertSame('wxTestAppId', $response['params']['appid']);
        $this->assertSame('testMerchantId', $response['params']['mch_id']);
        $this->assertSame('bar', $response['params']['foo']);
    }

    /**
     * Test query().
     */
    public function testQuery()
    {
        $api = $this->getAPI();
        $response = $api->query('testTradeNoFoo');

        $this->assertSame($api->wrapApi(API::API_QUERY), $response['api']);
        $this->assertSame('testTradeNoFoo', $response['params']['out_trade_no']);

        $response = $api->query('testTradeNoBar', API::TRANSACTION_ID);

        $this->assertSame($api->wrapApi(API::API_QUERY), $response['api']);
        $this->assertSame('testTradeNoBar', $response['params']['transaction_id']);

        $response = $api->queryByTransactionId('testTransactionId');
        $this->assertSame($api->wrapApi(API::API_QUERY), $response['api']);
        $this->assertSame('testTransactionId', $response['params']['transaction_id']);
    }

    /**
     * Test close().
     */
    public function testClose()
    {
        $api = $this->getAPI();

        $response = $api->close('testTradeNo');
        $this->assertSame($api->wrapApi(API::API_CLOSE), $response['api']);
        $this->assertSame('testTradeNo', $response['params']['out_trade_no']);
    }

    /**
     * Test reverse().
     */
    public function testReverse()
    {
        $api = $this->getAPI();

        $response = $api->reverse('testTradeNo');
        $this->assertSame($api->wrapApi(API::API_REVERSE), $response['api']);
        $this->assertSame('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->reverse('testTransactionId', API::TRANSACTION_ID);
        $this->assertSame($api->wrapApi(API::API_REVERSE), $response['api']);
        $this->assertSame('testTransactionId', $response['params']['transaction_id']);
    }

    /**
     * Test refund.
     */
    public function testRefund()
    {
        $api = $this->getAPI();

        $response = $api->refund('testTradeNo', 'testRefundNo', 100);
        $this->assertSame($api->wrapApi(API::API_REFUND), $response['api']);
        $this->assertSame('testRefundNo', $response['params']['out_refund_no']);
        $this->assertSame(100, $response['params']['total_fee']);
        $this->assertSame(100, $response['params']['refund_fee']);
        $this->assertSame('CNY', $response['params']['refund_fee_type']);
        $this->assertSame('testMerchantId', $response['params']['op_user_id']);
        $this->assertSame('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->refund('testTradeNo', 'testRefundNo', 100, 50);
        $this->assertSame('testRefundNo', $response['params']['out_refund_no']);
        $this->assertSame(100, $response['params']['total_fee']);
        $this->assertSame(50, $response['params']['refund_fee']);

        $response = $api->refund('testTradeNo', 'testRefundNo', 100, 50);
        $this->assertSame('testRefundNo', $response['params']['out_refund_no']);
        $this->assertSame(100, $response['params']['total_fee']);
        $this->assertSame(50, $response['params']['refund_fee']);
    }

    /**
     * Test queryRefund().
     */
    public function testQueryRefund()
    {
        $api = $this->getAPI();

        $response = $api->queryRefund('testTradeNo');
        $this->assertSame($api->wrapApi(API::API_QUERY_REFUND), $response['api']);
        $this->assertSame('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->queryRefund('testTransactionId', API::TRANSACTION_ID);
        $this->assertSame($api->wrapApi(API::API_QUERY_REFUND), $response['api']);
        $this->assertSame('testTransactionId', $response['params']['transaction_id']);
    }

    /**
     * Test downloadBill().
     */
    public function testDownloadBill()
    {
        $http = \Mockery::mock(Http::class);

        $http->shouldReceive('request')->andReturnUsing(function ($api, $method, $options) {
            $params = XML::parse($options['body']);
            $response = \Mockery::mock(ResponseInterface::class);
            $response->shouldReceive('getBody')->andReturn(compact('api', 'params'));

            return $response;
        });

        $merchant = new Merchant([
                'fee_type' => 'CNY',
                'merchant_id' => 'testMerchantId',
                'app_id' => 'wxTestAppId',
                'device_info' => 'testDeviceInfo',
                'key' => 'testKey',
                'notify_url' => 'merchant_default_notify_url',
            ]);

        $api = \Mockery::mock('EasyWeChat\Payment\API[getHttp]', [$merchant])->shouldAllowMockingProtectedMethods();

        $api->shouldReceive('wrapApi')->passthru();
        $api->shouldReceive('getHttp')->andReturn($http);

        $response = $api->downloadBill('20150901');
        $this->assertSame($api->wrapApi(API::API_DOWNLOAD_BILL), $response['api']);
        $this->assertSame('20150901', $response['params']['bill_date']);
        $this->assertSame(API::BILL_TYPE_ALL, $response['params']['bill_type']);

        $response = $api->downloadBill('20150901', API::BILL_TYPE_SUCCESS);
        $this->assertSame($api->wrapApi(API::API_DOWNLOAD_BILL), $response['api']);
        $this->assertSame('20150901', $response['params']['bill_date']);
        $this->assertSame(API::BILL_TYPE_SUCCESS, $response['params']['bill_type']);
    }

    /**
     * Test urlShorten().
     */
    public function testUrlShorten()
    {
        $api = $this->getAPI();

        $response = $api->urlShorten('http://easywechat.org');

        $this->assertSame('https://api.mch.weixin.qq.com/tools/shorturl', $response['api']);
        $this->assertSame('http://easywechat.org', $response['params']['long_url']);

        $sandboxPayment = $this->getAPI(true);
        $response = $sandboxPayment->urlShorten('http://easywechat.org');

        $this->assertSame('https://api.mch.weixin.qq.com/tools/shorturl', $response['api']);
        $this->assertSame('http://easywechat.org', $response['params']['long_url']);
    }

    /**
     * Test authCodeToOpenId().
     */
    public function testAuthCodeToOpenId()
    {
        $api = $this->getAPI();

        $response = $api->authCodeToOpenId('authcode');

        $this->assertSame('https://api.mch.weixin.qq.com/tools/authcodetoopenid', $response['api']);
        $this->assertSame('authcode', $response['params']['auth_code']);

        $sandboxPayment = $this->getAPI(true);
        $response = $sandboxPayment->authCodeToOpenId('authcode');

        $this->assertSame('https://api.mch.weixin.qq.com/tools/authcodetoopenid', $response['api']);
        $this->assertSame('authcode', $response['params']['auth_code']);
    }

    /**
     * Test setMerchant() and getMerchant.
     */
    public function testMerchantGetterAndSetter()
    {
        $api = $this->getAPI();
        $merchant = \Mockery::mock(Merchant::class);
        $api->setMerchant($merchant);

        $this->assertSame($merchant, $api->getMerchant());
    }
}
