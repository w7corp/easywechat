<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Card;

use EasyWeChat\OfficialAccount\Card\InvoiceClient;
use EasyWeChat\Tests\TestCase;

class InvoiceClientTest extends TestCase
{
    public function testSet()
    {
        $client = $this->mockApiClient(InvoiceClient::class);

        $params = [
            'paymch_info' => [
                'mchid' => 'mock-mchid',
                's_pappid' => 'mock-s-appid',
            ],
        ];

        $client->expects()->httpPostJson('card/invoice/setbizattr', $params, ['action' => 'set_pay_mch'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->set('mock-mchid', 'mock-s-appid'));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(InvoiceClient::class);

        $client->expects()->httpPostJson('card/invoice/setbizattr', [], ['action' => 'get_pay_mch'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get());
    }

    public function testSetAuthField()
    {
        $client = $this->mockApiClient(InvoiceClient::class);

        $params = [
            'auth_field' => [
                'user_field' => ['mock-user-data'],
                'biz_field' => ['mock-biz-data'],
            ],
        ];

        $client->expects()->httpPostJson('card/invoice/setbizattr', $params, ['action' => 'set_auth_field'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setAuthField(['mock-user-data'], ['mock-biz-data']));
    }

    public function testGetAuthField()
    {
        $client = $this->mockApiClient(InvoiceClient::class);

        $client->expects()->httpPostJson('card/invoice/setbizattr', [], ['action' => 'get_auth_field'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAuthField());
    }

    public function testGetAuthData()
    {
        $client = $this->mockApiClient(InvoiceClient::class);

        $params = [
            'order_id' => 'mock-order-id',
            's_appid' => 'mock-s-appid',
        ];

        $client->expects()->httpPost('card/invoice/getauthdata', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAuthData('mock-s-appid', 'mock-order-id'));
    }
}
