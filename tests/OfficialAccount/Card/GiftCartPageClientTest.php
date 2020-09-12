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

use EasyWeChat\OfficialAccount\Card\GiftCardPageClient;
use EasyWeChat\Tests\TestCase;

class GiftCartPageClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(GiftCardPageClient::class);

        $params = [
            'page_title' => 'mock-page-title',
            'support_multi' => true,
            'banner_pic_url' => 'mock-banner-pic-url',
            'theme_list' => 'mock-theme-list',
            //xxxx
        ];

        $client->expects()->httpPostJson('card/giftcard/page/add', ['page' => $params])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add($params));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(GiftCardPageClient::class);

        $params = [
            'page_id' => 'mock-page-id',
        ];

        $client->expects()->httpPostJson('card/giftcard/page/get', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('mock-page-id'));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(GiftCardPageClient::class);

        $params = [
            'page' => [
                'page_id' => 'mock-page-id',
                'banner_pic_url' => 'mock-banner-pic-url',
                'theme_list' => ['mock-theme-list'],
            ],
        ];

        $client->expects()->httpPostJson('card/giftcard/page/update', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update('mock-page-id', 'mock-banner-pic-url', ['mock-theme-list']));
    }

    public function testList()
    {
        $client = $this->mockApiClient(GiftCardPageClient::class);

        $client->expects()->httpPostJson('card/giftcard/page/batchget')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list());
    }

    public function testSetMaintain()
    {
        $client = $this->mockApiClient(GiftCardPageClient::class);

        $client->expects()->httpPostJson('card/giftcard/maintain/set', ['all' => true, 'maintain' => true])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setMaintain());

        $client->expects()->httpPostJson('card/giftcard/maintain/set', ['page_id' => 'mock-page-id', 'maintain' => true])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setMaintain('mock-page-id'));
    }
}
