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

use EasyWeChat\OfficialAccount\Card\GiftCardClient;
use EasyWeChat\Tests\TestCase;

class GiftCartClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(GiftCardClient::class);

        $params = [
            'sub_mch_id' => 'mock-sub-mch-id',
        ];

        $client->expects()->httpPostJson('card/giftcard/pay/whitelist/add', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add('mock-sub-mch-id'));
    }

    public function testBind()
    {
        $client = $this->mockApiClient(GiftCardClient::class);

        $params = [
            'sub_mch_id' => 'mock-sub-mch-id',
            'wxa_appid' => 'mock-wxa-appid',
        ];

        $client->expects()->httpPostJson('card/giftcard/pay/submch/bind', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->bind('mock-sub-mch-id', 'mock-wxa-appid'));
    }

    public function testSet()
    {
        $client = $this->mockApiClient(GiftCardClient::class);

        $params = [
            'wxa_appid' => 'mock-wxa-appid',
            'page_id' => 'mock-page-id',
        ];

        $client->expects()->httpPostJson('card/giftcard/wxa/set', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->set('mock-wxa-appid', 'mock-page-id'));
    }
}
