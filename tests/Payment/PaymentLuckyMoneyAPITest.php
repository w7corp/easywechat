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
use EasyWeChat\Payment\LuckyMoney\API;
use EasyWeChat\Payment\Merchant;
use EasyWeChat\Support\XML;
use EasyWeChat\Tests\TestCase;

class PaymentLuckyMoneyAPITest extends TestCase
{
    public static function setUpBeforeClass()
    {
        $_SERVER['HTTP_CLIENT_IP'] = '127.0.0.1';
    }

    /**
     * Build API instance.
     *
     * @return API
     */
    public function getAPI()
    {
        $http = \Mockery::mock(Http::class);

        $http->shouldReceive('request')->andReturnUsing(function ($api, $method, $options) {
            $options['body'] = XML::parse($options['body']);

            return XML::build(compact('api', 'options'));
        });

        $merchant = new Merchant([
                'merchant_id' => 'testMerchantId',
                'app_id' => 'wxTestAppId',
                'key' => 'testKey',
                'cert_path' => 'testCertPath',
                'key_path' => 'testKeyPath',
            ]);

        $api = \Mockery::mock('EasyWeChat\Payment\LuckyMoney\API[getHttp]', [$merchant]);
        $api->shouldReceive('getHttp')->andReturn($http);

        return $api;
    }

    /**
     * Test prepare().
     */
    public function testPrepare()
    {
        $api = $this->getAPI();

        $response = $api->prepare(['foo' => 'bar']);

        $this->assertSame(API::API_PREPARE, $response['api']);
        $this->assertSame('wxTestAppId', $response['options']['body']['wxappid']);
        $this->assertSame('testMerchantId', $response['options']['body']['mch_id']);

        $this->assertSame('1000052601', $response['options']['body']['auth_mchid']);
        $this->assertSame('wxbf42bd79c4391863', $response['options']['body']['auth_appid']);

        $this->assertSame('ALL_RAND', $response['options']['body']['amt_type']);

        $this->assertSame('bar', $response['options']['body']['foo']);
    }

    /**
     * Test query().
     */
    public function testQuery()
    {
        $api = $this->getAPI();
        $response = $api->query('testTradeNoFoo');

        $this->assertSame(API::API_QUERY, $response['api']);
        $this->assertSame('testTradeNoFoo', $response['options']['body']['mch_billno']);
    }

    /**
     * Test send().
     */
    public function testSend()
    {
        $api = $this->getAPI();

        $response = $api->send(['foo' => 'bar'], API::TYPE_NORMAL);
        $this->assertSame(API::API_SEND, $response['api']);
        $this->assertSame('bar', $response['options']['body']['foo']);

        $this->assertSame('wxTestAppId', $response['options']['body']['wxappid']);

        $response = $api->send(['foo' => 'bar'], API::TYPE_GROUP);
        $this->assertSame(API::API_SEND_GROUP, $response['api']);
        $this->assertSame('bar', $response['options']['body']['foo']);

        $this->assertSame('wxTestAppId', $response['options']['body']['wxappid']);
    }

    /**
     * Test sendNormal().
     */
    public function testSendNormal()
    {
        $api = $this->getAPI();

        $response = $api->sendNormal(['foo' => 'bar']);
        $this->assertSame(API::API_SEND, $response['api']);
        $this->assertSame('bar', $response['options']['body']['foo']);

        $this->assertSame(1, $response['options']['body']['total_num']);
    }

    /**
     * Test sendGroup().
     */
    public function testSendGroup()
    {
        $api = $this->getAPI();

        $response = $api->sendGroup(['foo' => 'bar']);
        $this->assertSame(API::API_SEND_GROUP, $response['api']);
        $this->assertSame('bar', $response['options']['body']['foo']);
    }

    /**
     * Test setMerchant() and getMerchant().
     */
    public function testMerchantGetterAndSetter()
    {
        $api = $this->getAPI();
        $merchant = \Mockery::mock(Merchant::class);
        $api->setMerchant($merchant);

        $this->assertSame($merchant, $api->getMerchant());
    }
}
