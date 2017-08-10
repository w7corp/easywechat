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

    public function testPrepare()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['prepare', 'safeRequest'], $app)->makePartial();

        $params = [
            'foo' => 'bar',
        ];

        $client->expects()->safeRequest('mmpaymkttransfers/hbpreorder', \Mockery::on(function ($paramsForSafeRequest) use ($app, $params) {
            $this->assertSame($paramsForSafeRequest['foo'], $params['foo']);
            $this->assertSame($paramsForSafeRequest['wxappid'], $app['merchant']->app_id);
            $this->assertSame($paramsForSafeRequest['auth_mchid'], '1000052601');
            $this->assertSame($paramsForSafeRequest['auth_appid'], 'wxbf42bd79c4391863');
            $this->assertSame($paramsForSafeRequest['amt_type'], 'ALL_RAND');

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->prepare($params));
    }

    public function testQuery()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['query', 'safeRequest'], $app)->makePartial();

        $mchBillNo = '123456';

        $params = [
            'appid' => $app['merchant']->app_id,
            'mch_billno' => $mchBillNo,
            'bill_type' => 'MCHT',
        ];

        $client->expects()->safeRequest('mmpaymkttransfers/gethbinfo', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->query($mchBillNo));
    }

    public function testSend()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $app)->makePartial();

        $params = [
            'client_id' => '162.168.0.1',
        ];

        $paramsForSafeRequest = array_merge($params, [
            'wxappid' => $app['merchant']->app_id,
        ]);

        // type === 'NORMAL'
        $client->expects()->safeRequest('mmpaymkttransfers/sendredpack', $paramsForSafeRequest)->andReturn('mock-result-normal');

        $this->assertSame('mock-result-normal', $client->send($params, Client::TYPE_NORMAL));

        // type === 'GROUP'
        unset($paramsForSafeRequest['client_ip']);

        $client->expects()->safeRequest('mmpaymkttransfers/sendgroupredpack', $paramsForSafeRequest)->andReturn('mock-result-group');

        $this->assertSame('mock-result-group', $client->send($params, Client::TYPE_GROUP));
    }

    public function testSendNormal()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'sendNormal'], $app)->makePartial();

        $params = [];

        $paramsDefault = [
            'total_num' => 1,
            'client_ip' => $params['client_ip'] ?? Support\get_server_ip(),
        ];

        $client->expects()->send(array_merge($params, $paramsDefault), Client::TYPE_NORMAL)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->sendNormal($params));
    }

    public function testSendGroup()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'sendGroup'], $app)->makePartial();

        $params = ['foo' => 'bar'];

        $client->expects()->send(array_merge($params, ['amt_type' => 'ALL_RAND']), Client::TYPE_GROUP)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->sendGroup($params));
    }

    public function testPrepends()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, 'prepends', $app)->makePartial();

        $this->assertNotEmpty($client->prepends());
        $this->assertArrayHasKey('mch_id', $client->prepends());
        $this->assertSame($app['merchant']->merchant_id, $client->prepends()['mch_id']);
    }
}
