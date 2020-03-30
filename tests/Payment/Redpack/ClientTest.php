<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Redpack;

use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Redpack\Client;
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

    public function testQuery()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['safeRequest'], $app)->makePartial();

        $client->expects()->safeRequest('mmpaymkttransfers/gethbinfo', [
            'appid' => $app['config']->app_id,
            'mch_billno' => '123456',
            'bill_type' => 'MCHT',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->info(['mch_billno' => '123456']));
    }

    public function testSendNormal()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->makeApp())->makePartial();
        $client->expects()->safeRequest('mmpaymkttransfers/sendredpack', [
            'total_num' => 1,
            'client_ip' => Support\get_server_ip(),
            'wxappid' => 'wx123456',
            'send_name' => 'foo',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->sendNormal(['send_name' => 'foo']));
    }

    public function testSendGroup()
    {
        $client = $this->mockApiClient(Client::class, ['safeRequest'], $this->makeApp())->makePartial();

        $params = [
            'foo' => 'bar',
        ];

        $client->expects()->safeRequest(
            'mmpaymkttransfers/sendgroupredpack',
            array_merge($params, ['amt_type' => 'ALL_RAND', 'wxappid' => 'wx123456'])
        )->andReturn('mock-result');

        $this->assertSame('mock-result', $client->sendGroup($params));
    }
}
