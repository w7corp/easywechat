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
            'mch_id' => 'foo-mcherant-id',
            'key' => 'foo-mcherant-key',
            'sub_appid' => 'foo-sub-appid',
            'sub_mch_id' => 'foo-sub-mch-id',
        ], $config));
    }

    public function testInfo()
    {
        $app = $this->makeApp();

        $client = $this->mockApiClient(Client::class, ['safeRequest'], $app)->makePartial();

        $params = ['partner_trade_no' => 'bar'];

        $client->expects()->safeRequest('mmpaymkttransfers/gettransferinfo', \Mockery::on(function ($paramsForSafeRequest) use ($app) {
            $this->assertSame($paramsForSafeRequest['partner_trade_no'], 'bar');
            $this->assertSame($paramsForSafeRequest['appid'], $app['config']->app_id);
            $this->assertSame($paramsForSafeRequest['mch_id'], $app['config']->mch_id);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->info($params));
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
            $this->assertSame($paramsForSafeRequest['mchid'], $app['config']->mch_id);
            $this->assertSame($paramsForSafeRequest['mch_appid'], $app['config']->app_id);

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send($params));
    }
}
