<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Transfer;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Transfer\Client;
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

    public function testQuery()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['query', 'safeRequest'], $app)->makePartial();

        $mchBillNo = 'foo';

        $client->expects()->safeRequest('mmpaymkttransfers/gettransferinfo', \Mockery::on(function ($paramsForSafeRequest) use ($app, $mchBillNo) {
            $this->assertSame($paramsForSafeRequest['partner_trade_no'], $mchBillNo);
            $this->assertSame($paramsForSafeRequest['appid'], $app['merchant']->app_id);
            $this->assertSame($paramsForSafeRequest['mch_id'], $app['merchant']->merchant_id);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->query($mchBillNo));
    }

    public function testSend()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $app)->makePartial();

        $params = [
            'foo' => 'bar',
        ];

        $client->expects()->safeRequest('mmpaymkttransfers/promotion/transfers', \Mockery::on(function ($paramsForSafeRequest) use ($params, $app) {
            $this->assertSame($params['foo'], $paramsForSafeRequest['foo']);
            $this->assertSame($paramsForSafeRequest['mchid'], $app['merchant']->merchant_id);
            $this->assertSame($paramsForSafeRequest['mch_appid'], $app['merchant']->app_id);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send($params));
    }
}
