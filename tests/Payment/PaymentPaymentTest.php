<?php

/*
 * This file is part of the EasyWeChat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Payment\API;
use EasyWeChat\Payment\Merchant;
use EasyWeChat\Payment\Payment;

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
