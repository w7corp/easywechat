<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Coupon;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Coupon\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * Make Application.
     *
     * @param array $config
     */
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_appid' => 'foo-sub-appid',
            'sub_mch_id' => 'foo-sub-mch-id',
        ], $config));
    }

    public function testSend()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $app)->makePartial();

        $params = ['foo' => 'bar'];

        $client->expects()->safeRequest('mmpaymkttransfers/send_coupon', array_merge($params, ['openid_count' => 1]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send($params));
    }

    public function testQueryStock()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'queryStock'], $app)->makePartial();

        $params = ['foo' => 'bar'];

        $client->expects()->request('mmpaymkttransfers/query_coupon_stock', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->queryStock($params));
    }

    public function testQuery()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'query'], $app)->makePartial();

        $params = ['foo' => 'bar'];

        $client->expects()->request('mmpaymkttransfers/querycouponsinfo', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->query($params));
    }

    public function testPrepends()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, 'prepends', $app)->makePartial();

        $this->assertNotEmpty($client->prepends());
        $this->assertArrayHasKey('mch_id', $client->prepends());
        $this->assertArrayHasKey('appid', $client->prepends());
        $this->assertSame($app['merchant']->merchant_id, $client->prepends()['mch_id']);
        $this->assertSame($app['merchant']->app_id, $client->prepends()['appid']);
    }
}
