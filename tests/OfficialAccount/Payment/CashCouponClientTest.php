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

use EasyWeChat\Foundation\Core\Http;
use EasyWeChat\OfficialAccount\Payment\CashCoupon\Client as API;
use EasyWeChat\OfficialAccount\Payment\Merchant;
use EasyWeChat\Support\XML;
use EasyWeChat\Tests\TestCase;

class CashCouponClientTest extends TestCase
{
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

        $api = \Mockery::mock('EasyWeChat\OfficialAccount\Payment\CashCoupon\Client[getHttp]', [$merchant]);
        $api->shouldReceive('getHttp')->andReturn($http);

        return $api;
    }

    /**
     * Test send().
     */
    public function testSend()
    {
        $api = $this->getAPI();

        $response = $api->send(['foo' => 'bar']);

        $this->assertEquals(API::API_SEND, $response['api']);
        $this->assertEquals('wxTestAppId', $response['options']['body']['appid']);
        $this->assertEquals('testMerchantId', $response['options']['body']['mch_id']);

        $this->assertEquals('bar', $response['options']['body']['foo']);
    }

    public function testQueryStock()
    {
        $api = $this->getAPI();

        $response = $api->queryStock(['coupon_stock_id' => '1234']);

        $this->assertEquals(API::API_QUERY_STOCK, $response['api']);
        $this->assertEquals('wxTestAppId', $response['options']['body']['appid']);
        $this->assertEquals('testMerchantId', $response['options']['body']['mch_id']);

        $this->assertEquals('1234', $response['options']['body']['coupon_stock_id']);
    }

    public function testQuery()
    {
        $api = $this->getAPI();

        $response = $api->query(['foo' => 'bar']);

        $this->assertEquals(API::API_QUERY, $response['api']);
        $this->assertEquals('wxTestAppId', $response['options']['body']['appid']);
        $this->assertEquals('testMerchantId', $response['options']['body']['mch_id']);

        $this->assertEquals('bar', $response['options']['body']['foo']);
    }
}
