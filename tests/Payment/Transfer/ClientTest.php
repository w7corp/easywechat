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
    public function testQuery()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-merchant-id',
        ]);
        $client = $this->mockApiClient(Client::class, ['query', 'safeRequest'], $app)->makePartial();

        $mchBillNo = 'foo';

        $params = [
            'appid' => $app['merchant']->app_id,
            'mch_id' => $app['merchant']->merchant_id,
            'partner_trade_no' => $mchBillNo,
        ];

        $client->expects()->safeRequest('mmpaymkttransfers/gettransferinfo', $params)
            ->once()
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->query($mchBillNo));
    }

    public function testSend()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'merchant_id' => 'foo-merchant-id',
        ]);
        $client = $this->mockApiClient(Client::class, ['send', 'safeRequest'], $app)->makePartial();

        $params = [
            'foo' => 'bar',
        ];

        $paramsForSafeRequest = array_merge($params, [
            'mchid' => $app['merchant']->merchant_id,
            'mch_appid' => $app['merchant']->app_id,
        ]);

        $client->expects()->safeRequest('mmpaymkttransfers/promotion/transfers', $paramsForSafeRequest)
            ->once()
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->send($params));
    }
}
