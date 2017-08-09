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
    public function testPrepare()
    {
        $app = new Application([
            'app_id' => 'wx123456',
        ]);
        $client = $this->mockApiClient(Client::class, ['prepare', 'safeRequest'], $app)->makePartial();

        $params = [
            'foo' => 'bar',
        ];

        $paramsForSafeRequest = array_merge($params, [
            'wxappid' => $app['merchant']->app_id,
            'auth_mchid' => '1000052601',
            'auth_appid' => 'wxbf42bd79c4391863',
            'amt_type' => 'ALL_RAND',
        ]);

        $client->expects()->safeRequest('mmpaymkttransfers/hbpreorder', $paramsForSafeRequest)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->prepare($params));
    }

    public function testQuery()
    {
        $app = new Application([
            'app_id' => 'wx123456',
        ]);
        $client = $this->mockApiClient(Client::class, ['query', 'safeRequest'], $app)->makePartial();

        $mchBillNo = '123456';

        $params = [
            'appid' => $app['merchant']->app_id,
            'mch_billno' => $mchBillNo,
            'bill_type' => 'MCHT',
        ];

        $client->expects()->safeRequest('mmpaymkttransfers/gethbinfo', $params)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->query($mchBillNo));
    }

    public function testSend()
    {
        $app = new Application([
            'app_id' => 'wx123456',
        ]);
        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $app)->makePartial();

        $params = [
            'client_id' => '162.168.0.1',
        ];

        $paramsForSafeRequest = array_merge($params, [
            'wxappid' => $app['merchant']->app_id,
        ]);

        // type === 'NORMAL'
        $type = Client::TYPE_NORMAL;
        $endpoint = 'mmpaymkttransfers/sendredpack';

        $client->expects()->safeRequest($endpoint, $paramsForSafeRequest)->andReturn('mock-result-normal')->once();

        $this->assertSame('mock-result-normal', $client->send($params, $type));

        // type === 'GROUP'
        $type = Client::TYPE_GROUP;
        $endpoint = 'mmpaymkttransfers/sendgroupredpack';
        unset($paramsForSafeRequest['client_ip']);

        $client->expects()->safeRequest($endpoint, $paramsForSafeRequest)->andReturn('mock-result-group')->once();

        $this->assertSame('mock-result-group', $client->send($params, $type));
    }

    public function testSendNormal()
    {
        $app = new Application();
        $client = $this->mockApiClient(Client::class, ['send', 'sendNormal'], $app)->makePartial();

        $params = [];

        $paramsDefault = [
            'total_num' => 1,
            'client_ip' => $params['client_ip'] ?? Support\get_server_ip(),
        ];

        $client->expects()->send(array_merge($params, $paramsDefault), Client::TYPE_NORMAL)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->sendNormal($params));
    }

    public function testSendGroup()
    {
        $app = new Application();
        $client = $this->mockApiClient(Client::class, ['send', 'sendGroup'], $app)->makePartial();

        $params = [];

        $client->expects()->send(array_merge($params, ['amt_type' => 'ALL_RAND']), Client::TYPE_GROUP)->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->sendGroup($params));
    }

    public function testPrepends()
    {
        $app = new Application([
            'app_id' => '123456',
            'merchant_id' => 'foo-merchant-id',
        ]);

        $client = $this->mockApiClient(Client::class, 'prepends', $app)->makePartial();

        $this->assertNotEmpty($client->prepends());
        $this->assertArrayHasKey('mch_id', $client->prepends());
        $this->assertSame('foo-merchant-id', $client->prepends()['mch_id']);
    }
}
