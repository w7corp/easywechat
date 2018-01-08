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
    private function makeApp($config = [])
    {
        return new Application(array_merge([
            'app_id' => 'wx123456',
            'mch_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_appid' => 'foo-sub-appid',
            'sub_mch_id' => 'foo-sub-mch-id',
        ], $config));
    }

    public function testSend()
    {
        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $this->makeApp())->makePartial();

        $params = [
            'foo' => 'bar',
            'appid' => 'wx123456',
        ];

        $client->expects()->safeRequest('mmpaymkttransfers/send_coupon', array_merge($params, ['openid_count' => 1]))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send($params));
    }

    public function testStock()
    {
        $client = $this->mockApiClient(Client::class, ['send', 'queryStock'], $this->makeApp())->makePartial();

        $params = [
            'foo' => 'bar',
            'appid' => 'wx123456',
        ];

        $client->expects()->request('mmpaymkttransfers/query_coupon_stock', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->stock($params));
    }

    public function testInfo()
    {
        $client = $this->mockApiClient(Client::class, ['send', 'query'], $this->makeApp())->makePartial();

        $params = [
            'foo' => 'bar',
            'appid' => 'wx123456',
        ];

        $client->expects()->request('mmpaymkttransfers/querycouponsinfo', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->info($params));
    }
}
