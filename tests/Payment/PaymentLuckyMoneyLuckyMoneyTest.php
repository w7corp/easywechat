<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Payment\LuckyMoney\API;
use EasyWeChat\Payment\LuckyMoney\LuckyMoney;
use EasyWeChat\Payment\Merchant;

class PaymentLuckyMoneyLuckyMoneyTest extends TestCase
{
    /**
     * Return LuckyMoney instance.
     *
     * @return LuckyMoney
     */
    public function getLuckyMoney()
    {
        $merchant = new Merchant([
                'merchant_id' => 'testMerchantId',
                'app_id' => 'wxTestAppId',
                'key' => 'testKey',
                'cert_path' => 'testCertPath',
                'key_path' => 'testKeyPath',
            ]);

        return new LuckyMoney($merchant);
    }

    /**
     * Test setMerchant()、getMerchant()、setAPI() and getAPI().
     */
    public function testSetterAndGetter()
    {
        $luckyMoney = $this->getLuckyMoney();

        $this->assertInstanceOf(API::class, $luckyMoney->getAPI());
        $this->assertInstanceOf(Merchant::class, $luckyMoney->getMerchant());

        $api = Mockery::mock(API::class);
        $luckyMoney->setAPI($api);
        $this->assertEquals($api, $luckyMoney->getAPI());

        $merchant = Mockery::mock(Merchant::class);
        $api = Mockery::mock(API::class);

        $luckyMoney->setAPI($api);
        $luckyMoney->setMerchant($merchant);
        $this->assertEquals($merchant, $luckyMoney->getMerchant());
        $this->assertEquals($merchant, $merchant);
    }
}
