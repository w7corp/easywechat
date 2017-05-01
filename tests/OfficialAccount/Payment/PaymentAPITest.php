<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Payment;

use EasyWeChat\OfficialAccount\Core\Http;
use EasyWeChat\OfficialAccount\Payment\API;
use EasyWeChat\OfficialAccount\Payment\Merchant;
use EasyWeChat\OfficialAccount\Payment\Order;
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

        $api = \Mockery::mock('EasyWeChat\OfficialAccount\Payment\API[getHttp,getCache]', [$merchant])
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

        $this->assertEquals($api->wrapApi(API::API_PREPARE_ORDER), $response['api']);
        $this->assertEquals('wxTestAppId', $response['params']['appid']);
        $this->assertEquals('merchant_default_notify_url', $response['params']['notify_url']);
        $this->assertEquals('testMerchantId', $response['params']['mch_id']);
        $this->assertEquals('bar', $response['params']['foo']);
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

        $this->assertEquals($api->wrapApi(API::API_PAY_ORDER), $response['api']);
        $this->assertEquals('wxTestAppId', $response['params']['appid']);
        $this->assertEquals('testMerchantId', $response['params']['mch_id']);
        $this->assertEquals('bar', $response['params']['foo']);
    }

    /**
     * Test query().
     */
    public function testQuery()
    {
        $api = $this->getAPI();
        $response = $api->query('testTradeNoFoo');

        $this->assertEquals($api->wrapApi(API::API_QUERY), $response['api']);
        $this->assertEquals('testTradeNoFoo', $response['params']['out_trade_no']);

        $response = $api->query('testTradeNoBar', API::TRANSACTION_ID);

        $this->assertEquals($api->wrapApi(API::API_QUERY), $response['api']);
        $this->assertEquals('testTradeNoBar', $response['params']['transaction_id']);

        $response = $api->queryByTransactionId('testTransactionId');
        $this->assertEquals($api->wrapApi(API::API_QUERY), $response['api']);
        $this->assertEquals('testTransactionId', $response['params']['transaction_id']);
    }

    /**
     * Test close().
     */
    public function testClose()
    {
        $api = $this->getAPI();

        $response = $api->close('testTradeNo');
        $this->assertEquals($api->wrapApi(API::API_CLOSE), $response['api']);
        $this->assertEquals('testTradeNo', $response['params']['out_trade_no']);
    }

    /**
     * Test reverse().
     */
    public function testReverse()
    {
        $api = $this->getAPI();

        $response = $api->reverse('testTradeNo');
        $this->assertEquals($api->wrapApi(API::API_REVERSE), $response['api']);
        $this->assertEquals('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->reverse('testTransactionId', API::TRANSACTION_ID);
        $this->assertEquals($api->wrapApi(API::API_REVERSE), $response['api']);
        $this->assertEquals('testTransactionId', $response['params']['transaction_id']);
    }

    /**
     * Test refund.
     */
    public function testRefund()
    {
        $api = $this->getAPI();

        $response = $api->refund('testTradeNo', 'testRefundNo', 100);
        $this->assertEquals($api->wrapApi(API::API_REFUND), $response['api']);
        $this->assertEquals('testRefundNo', $response['params']['out_refund_no']);
        $this->assertEquals(100, $response['params']['total_fee']);
        $this->assertEquals(100, $response['params']['refund_fee']);
        $this->assertEquals('CNY', $response['params']['refund_fee_type']);
        $this->assertEquals('testMerchantId', $response['params']['op_user_id']);
        $this->assertEquals('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->refund('testTradeNo', 'testRefundNo', 100, 50);
        $this->assertEquals('testRefundNo', $response['params']['out_refund_no']);
        $this->assertEquals(100, $response['params']['total_fee']);
        $this->assertEquals(50, $response['params']['refund_fee']);

        $response = $api->refund('testTradeNo', 'testRefundNo', 100, 50);
        $this->assertEquals('testRefundNo', $response['params']['out_refund_no']);
        $this->assertEquals(100, $response['params']['total_fee']);
        $this->assertEquals(50, $response['params']['refund_fee']);
    }

    /**
     * Test queryRefund().
     */
    public function testQueryRefund()
    {
        $api = $this->getAPI();

        $response = $api->queryRefund('testTradeNo');
        $this->assertEquals($api->wrapApi(API::API_QUERY_REFUND), $response['api']);
        $this->assertEquals('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->queryRefund('testTransactionId', API::TRANSACTION_ID);
        $this->assertEquals($api->wrapApi(API::API_QUERY_REFUND), $response['api']);
        $this->assertEquals('testTransactionId', $response['params']['transaction_id']);
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

        $api = \Mockery::mock('EasyWeChat\OfficialAccount\Payment\API[getHttp]', [$merchant])->shouldAllowMockingProtectedMethods();

        $api->shouldReceive('wrapApi')->passthru();
        $api->shouldReceive('getHttp')->andReturn($http);

        $response = $api->downloadBill('20150901');
        $this->assertEquals($api->wrapApi(API::API_DOWNLOAD_BILL), $response['api']);
        $this->assertEquals('20150901', $response['params']['bill_date']);
        $this->assertEquals(API::BILL_TYPE_ALL, $response['params']['bill_type']);

        $response = $api->downloadBill('20150901', API::BILL_TYPE_SUCCESS);
        $this->assertEquals($api->wrapApi(API::API_DOWNLOAD_BILL), $response['api']);
        $this->assertEquals('20150901', $response['params']['bill_date']);
        $this->assertEquals(API::BILL_TYPE_SUCCESS, $response['params']['bill_type']);
    }

    /**
     * Test urlShorten().
     */
    public function testUrlShorten()
    {
        $api = $this->getAPI();
        $response = $api->urlShorten('http://easywechat.org');

        $this->assertEquals('https://api.mch.weixin.qq.com/tools/shorturl', $response['api']);
        $this->assertEquals('http://easywechat.org', $response['params']['long_url']);

        $sandboxPayment = $this->getAPI(true);
        $response = $sandboxPayment->urlShorten('http://easywechat.org');

        $this->assertEquals('https://api.mch.weixin.qq.com/tools/shorturl', $response['api']);
        $this->assertEquals('http://easywechat.org', $response['params']['long_url']);
    }

    /**
     * Test authCodeToOpenId().
     */
    public function testAuthCodeToOpenId()
    {
        $api = $this->getAPI();

        $response = $api->authCodeToOpenId('authcode');

        $this->assertEquals('https://api.mch.weixin.qq.com/tools/authcodetoopenid', $response['api']);
        $this->assertEquals('authcode', $response['params']['auth_code']);

        $sandboxPayment = $this->getAPI(true);
        $response = $sandboxPayment->authCodeToOpenId('authcode');

        $this->assertEquals('https://api.mch.weixin.qq.com/tools/authcodetoopenid', $response['api']);
        $this->assertEquals('authcode', $response['params']['auth_code']);
    }

    /**
     * Test setMerchant() and getMerchant.
     */
    public function testMerchantGetterAndSetter()
    {
        $api = $this->getAPI();
        $merchant = \Mockery::mock(Merchant::class);
        $api->setMerchant($merchant);

        $this->assertEquals($merchant, $api->getMerchant());
    }
}
