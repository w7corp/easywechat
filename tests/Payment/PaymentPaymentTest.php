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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentPaymentTest extends PHPUnit_Framework_TestCase
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

        $this->setExpectedException(FaultException::class, 'Invalid request XML.', 400);

        $payment->handleNotify(function () {});
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
