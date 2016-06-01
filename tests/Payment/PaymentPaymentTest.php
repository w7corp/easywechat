<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Payment\API;
use EasyWeChat\Payment\Merchant;
use EasyWeChat\Payment\Notify;
use EasyWeChat\Payment\Payment;
use EasyWeChat\Support\XML;
use Overtrue\Socialite\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentPaymentTest extends TestCase
{
    /**
     * Return Payment instance.
     *
     * @return Payment
     */
    public function getPayment()
    {
        $merchant = new Merchant([
                'fee_type' => 'CNY',
                'merchant_id' => 'testMerchantId',
                'app_id' => 'wxTestAppId',
                'device_info' => 'testDeviceInfo',
                'key' => 'testKey',
            ]);

        return new Payment($merchant);
    }

    /**
     * Test scheme().
     */
    public function testScheme()
    {
        $payment = $this->getPayment();

        $url = $payment->scheme('foo');

        $this->assertContains(Payment::SCHEME_PATH, $url);
        $this->assertContains('product_id=foo', $url);
        $this->assertContains('appid=wxTestAppId', $url);
        $this->assertContains('mch_id=testMerchantId', $url);
        $this->assertContains('time_stamp=', $url);
        $this->assertContains('nonce_str=', $url);
        $this->assertContains('sign=', $url);
    }

    /**
     * Test handleNotify().
     */
    public function testHandleNotifyWithInvalidRequest()
    {
        $merchant = new Merchant(['key' => 'different_sign_key']);
        $payment = Mockery::mock(Payment::class.'[getNotify]', [$merchant]);
        $request = Request::create('/callback', 'POST', [], [], [], [], '<xml><foo>bar</foo></xml>');
        $notify = Mockery::mock(Notify::class.'[isValid]', [$merchant, $request]);
        $notify->shouldReceive('isValid')->andReturn(false);
        $payment->shouldReceive('getNotify')->andReturn($notify);

        $this->setExpectedException(FaultException::class, 'Invalid request payloads.', 400);

        $payment->handleNotify(function () {
        });
    }

    /**
     * Test handleNotify().
     */
    public function testHandleNotify()
    {
        $merchant = new Merchant(['key' => 'different_sign_key']);
        $payment = Mockery::mock(Payment::class.'[getNotify]', [$merchant]);
        $request = Request::create('/callback', 'POST', [], [], [], [], '<xml><foo>bar</foo></xml>');
        $notify = Mockery::mock(Notify::class.'[isValid]', [$merchant, $request]);
        $notify->shouldReceive('isValid')->andReturn(true);
        $payment->shouldReceive('getNotify')->andReturn($notify);

        $response = $payment->handleNotify(function () {
            return true;
        });

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(XML::build([
                'return_code' => 'SUCCESS',
                'return_msg' => 'OK',
            ]), $response->getContent());

        $response = $payment->handleNotify(function () {
            return 'error_message';
        });

        $this->assertEquals(XML::build([
                'return_code' => 'FAIL',
                'return_msg' => 'error_message',
            ]), $response->getContent());

        $response = $payment->handleNotify(function () {
            return false;
        });

        $this->assertEquals(XML::build([
                'return_code' => 'FAIL',
                'return_msg' => '',
            ]), $response->getContent());
    }

    /**
     * test configForPayment.
     */
    public function testConfigForPayment()
    {
        $payment = $this->getPayment();

        $json = $payment->configForPayment('prepayId');

        $array = json_decode($json, true);
        $this->assertEquals('wxTestAppId', $array['appId']);
        $this->assertEquals('prepay_id=prepayId', $array['package']);
        $this->assertEquals('MD5', $array['signType']);
        $this->assertArrayHasKey('timeStamp', $array);
        $this->assertArrayHasKey('nonceStr', $array);
        $this->assertArrayHasKey('paySign', $array);
    }

    /**
     * test configForPayment.
     */
    public function testConfigForJSSDKPayment()
    {
        $payment = $this->getPayment();

        $config = $payment->configForJSSDKPayment('prepayId');

        $this->assertEquals('wxTestAppId', $config['appId']);
        $this->assertEquals('prepay_id=prepayId', $config['package']);
        $this->assertEquals('MD5', $config['signType']);
        $this->assertArrayHasKey('timestamp', $config);
        $this->assertArrayHasKey('nonceStr', $config);
        $this->assertArrayHasKey('paySign', $config);
    }

    /**
     * test configForAppPayment.
     */
    public function testConfigForAppPayment()
    {
        $payment = $this->getPayment();

        $array = $payment->configForAppPayment('prepayId');

        $this->assertEquals('wxTestAppId', $array['appid']);
        $this->assertEquals('prepayId', $array['prepayid']);
        $this->assertEquals('Sign=WXPay', $array['package']);
        $this->assertArrayHasKey('timestamp', $array);
        $this->assertArrayHasKey('noncestr', $array);
        $this->assertArrayHasKey('sign', $array);
    }

    /**
     * test configForShareAddress.
     */
    public function testConfigForShareAddress()
    {
        $payment = $this->getPayment();

        $json = $payment->configForShareAddress('accessToken');

        $array = json_decode($json, true);
        $this->assertEquals('wxTestAppId', $array['appId']);
        $this->assertEquals('jsapi_address', $array['scope']);
        $this->assertEquals('SHA1', $array['signType']);
        $this->assertArrayHasKey('timeStamp', $array);
        $this->assertArrayHasKey('nonceStr', $array);
        $this->assertArrayHasKey('addrSign', $array);

        $log = new stdClass();
        $log->called = false;
        $accessToken = Mockery::mock(AccessToken::class.'[getToken]', [['access_token' => 'mockToken']]);

        $accessToken->shouldReceive('getToken')->andReturnUsing(function () use ($log) {
            $log->called = true;

            return 'mockToken';
        });

        $json = $payment->configForShareAddress($accessToken);
        $this->assertTrue($log->called);
    }

    /**
     * test getNotify.
     */
    public function testGetNotify()
    {
        $payment = $this->getPayment();

        $this->assertInstanceOf(Notify::class, $payment->getNotify());
    }

    /**
     * Test setMerchant()、getMerchant()、setAPI() and getAPI().
     */
    public function testSetterAndGetter()
    {
        $payment = $this->getPayment();

        $this->assertInstanceOf(API::class, $payment->getAPI());
        $this->assertInstanceOf(Merchant::class, $payment->getMerchant());

        $api = Mockery::mock(API::class);
        $payment->setAPI($api);
        $this->assertEquals($api, $payment->getAPI());

        $merchant = Mockery::mock(Merchant::class);
        $api = Mockery::mock(API::class);

        $payment->setAPI($api);
        $payment->setMerchant($merchant);
        $this->assertEquals($merchant, $payment->getMerchant());
        $this->assertEquals($merchant, $merchant);
    }
}
