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

use EasyWeChat\Applications\Base\Core\Http;
use EasyWeChat\Applications\OfficialAccount\Payment\Client as API;
use EasyWeChat\Applications\OfficialAccount\Payment\Merchant;
use EasyWeChat\Applications\OfficialAccount\Payment\Order;
use EasyWeChat\Support\XML;
use EasyWeChat\Tests\TestCase;
use Psr\Http\Message\ResponseInterface;

class PaymentClientTest extends TestCase
{
    /**
     * Build API instance.
     *
     * @param bool $sandboxEnabled
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

        $api = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\Payment\Client[getHttp,getCache]', [$merchant]);

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

        $this->assertSame('https://api.mch.weixin.qq.com/pay/unifiedorder', $response['api']);
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

        $this->assertSame('https://api.mch.weixin.qq.com/pay/micropay', $response['api']);
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

        $this->assertSame('https://api.mch.weixin.qq.com/pay/orderquery', $response['api']);
        $this->assertSame('testTradeNoFoo', $response['params']['out_trade_no']);

        $response = $api->query('testTradeNoBar', API::TRANSACTION_ID);

        $this->assertSame('https://api.mch.weixin.qq.com/pay/orderquery', $response['api']);
        $this->assertSame('testTradeNoBar', $response['params']['transaction_id']);

        $response = $api->queryByTransactionId('testTransactionId');
        $this->assertSame('https://api.mch.weixin.qq.com/pay/orderquery', $response['api']);
        $this->assertSame('testTransactionId', $response['params']['transaction_id']);
    }

    /**
     * Test close().
     */
    public function testClose()
    {
        $api = $this->getAPI();

        $response = $api->close('testTradeNo');
        $this->assertSame('https://api.mch.weixin.qq.com/pay/closeorder', $response['api']);
        $this->assertSame('testTradeNo', $response['params']['out_trade_no']);
    }

    /**
     * Test reverse().
     */
    public function testReverse()
    {
        $api = $this->getAPI();

        $response = $api->reverse('testTradeNo');
        $this->assertSame('https://api.mch.weixin.qq.com/secapi/pay/reverse', $response['api']);
        $this->assertSame('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->reverse('testTransactionId', API::TRANSACTION_ID);
        $this->assertSame('https://api.mch.weixin.qq.com/secapi/pay/reverse', $response['api']);
        $this->assertSame('testTransactionId', $response['params']['transaction_id']);
    }

    /**
     * Test refund.
     */
    public function testRefund()
    {
        $api = $this->getAPI();

        $response = $api->refund('testTradeNo', 'testRefundNo', 100);
        $this->assertSame('https://api.mch.weixin.qq.com/secapi/pay/refund', $response['api']);
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
        $this->assertSame('https://api.mch.weixin.qq.com/pay/refundquery', $response['api']);
        $this->assertSame('testTradeNo', $response['params']['out_trade_no']);

        $response = $api->queryRefund('testTransactionId', API::TRANSACTION_ID);
        $this->assertSame('https://api.mch.weixin.qq.com/pay/refundquery', $response['api']);
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

        $api = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\Payment\Client[getHttp]', [$merchant])->shouldAllowMockingProtectedMethods();

        $api->shouldReceive('getHttp')->andReturn($http);

        $response = $api->downloadBill('20150901');
        $this->assertSame('https://api.mch.weixin.qq.com/pay/downloadbill', $response['api']);
        $this->assertSame('20150901', $response['params']['bill_date']);
        $this->assertSame(API::BILL_TYPE_ALL, $response['params']['bill_type']);

        $response = $api->downloadBill('20150901', API::BILL_TYPE_SUCCESS);
        $this->assertSame('https://api.mch.weixin.qq.com/pay/downloadbill', $response['api']);
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
