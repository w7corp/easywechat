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

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\OfficialAccount\Card\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testColors()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('card/getcolors')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->colors());
    }

    public function testCategories()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('card/getapplyprotocol')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->categories());
    }

    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $attributes = [
            'base_info' => [
                'brand_name' => '微信餐厅',
                'code_type' => 'CODE_TYPE_TEXT',
                'title' => '132元双人火锅套餐',
                'use_limit' => 100,
                'get_limit' => 3,
                // ...
            ],

            'advanced_info' => [
                'use_condition' => [
                'accept_category' => '鞋类',
                   'reject_category' => '阿迪达斯',
                   'can_use_with_other_discount' => true,
               ],
                //...
            ],
        ];
        $client->expects()->httpPostJson('card/create', [
            'card' => [
                'card_type' => 'MEMBER_CARD',
                'member_card' => $attributes,
            ],
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->create('member_card', $attributes));
    }

    public function testCreateQrCode()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/qrcode/create', ['foo', 'bar'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->createQrCode(['foo', 'bar']));
    }

    public function testGetQrCode()
    {
        $client = $this->mockApiClient(Client::class);

        $baseUri = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
        $params = [
            'ticket' => 'mock-ticket',
        ];
        $response = new Response(200, ['content-type' => 'image/jpeg'], 'mock-content');
        $client->expects()->requestRaw($baseUri, 'GET', $params)->andReturn($response)->once();

        $this->assertSame([
            'status' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'headers' => $response->getHeaders(),
            'body' => strval($response->getBody()),
            'url' => $baseUri.'?'.http_build_query($params),
        ], $client->getQrCode('mock-ticket'));
    }

    public function testGetQrCodeUrl()
    {
        $client = $this->mockApiClient(Client::class);

        $this->assertSame('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=mock-ticket', $client->getQrCodeUrl('mock-ticket'));
    }

    public function testCreateLandingPage()
    {
        $client = $this->mockApiClient(Client::class);

        $banner = 'mock-banner';
        $pageTitle = 'mock-title';
        $canShare = true;
        $scene = 'mock-scene';
        $cardList = ['foo', 'bar'];
        $client->expects()->httpPostJson('card/landingpage/create', [
            'banner' => $banner,
            'page_title' => $pageTitle,
            'can_share' => $canShare,
            'scene' => $scene,
            'card_list' => $cardList,
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->createLandingPage($banner, $pageTitle, $canShare, $scene, $cardList));
    }

    public function testGetHtml()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/mpnews/gethtml', ['card_id' => 'mock-card-id'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->getHtml('mock-card-id'));
    }

    public function testSetTestWhitelist()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/testwhitelist/set', ['openid' => ['mock-openid1', 'mock-openid2']])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->setTestWhitelist(['mock-openid1', 'mock-openid2']));
    }

    public function testSetTestWhitelistByName()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/testwhitelist/set', ['username' => ['mock-username1', 'mock-username2']])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->setTestWhitelistByName(['mock-username1', 'mock-username2']));
    }

    public function testGetUserCards()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/user/getcardlist', ['openid' => 'mock-openid', 'card_id' => 'mock-card-id'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->getUserCards('mock-openid', 'mock-card-id'));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/get', ['card_id' => 'mock-card-id'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->get('mock-card-id'));
    }

    public function testDelelte()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/delete', ['card_id' => 'mock-card-id'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->delete('mock-card-id'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/batchget', ['offset' => 0, 'count' => 10, 'status_list' => 'CARD_STATUS_VERIFY_OK'])
            ->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->list());

        $client->expects()->httpPostJson('card/batchget', ['offset' => 1, 'count' => 10, 'status_list' => 'CARD_STATUS_VERIFY_OK'])
            ->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->list(1));

        $client->expects()->httpPostJson('card/batchget', ['offset' => 1, 'count' => 10, 'status_list' => 'CARD_STATUS_VERIFY_OK'])
            ->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->list(1, 10));

        $client->expects()->httpPostJson('card/batchget', ['offset' => 1, 'count' => 10, 'status_list' => 'CUSTOM'])
            ->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->list(1, 10, 'CUSTOM'));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $attributes = [
            'base_info' => [
                'brand_name' => '微信餐厅',
                'code_type' => 'CODE_TYPE_TEXT',
                'title' => '132元双人火锅套餐',
                //...

                'use_limit' => 100,
                'get_limit' => 3,
                // ...
            ],

            'advanced_info' => [
                'use_condition' => [
                    'accept_category' => '鞋类',
                    'reject_category' => '阿迪达斯',
                    'can_use_with_other_discount' => true,
                ],
                //...
            ],
        ];

        // case 1
        $client->expects()->httpPostJson('card/update', [
            'card_id' => 'mock-card-id',
            'member_card' => [],
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->update('mock-card-id', 'member_card'));

        // case 2
        $client->expects()->httpPostJson('card/update', [
            'card_id' => 'mock-card-id',
            'member_card' => $attributes,
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->update('mock-card-id', 'member_card', $attributes));
    }

    public function testSetPayCell()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('card/paycell/set', ['card_id' => 'mock-card-id', 'is_open' => true])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->setPayCell('mock-card-id'));

        $client->expects()->httpPostJson('card/paycell/set', ['card_id' => 'mock-card-id', 'is_open' => false])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->setPayCell('mock-card-id', false));
    }

    public function testIncreaseStock()
    {
        $client = $this->mockApiClient(Client::class, ['updateStock']);

        $client->expects()->updateStock('mock-card-id', 10, 'increase')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->increaseStock('mock-card-id', 10));
    }

    public function testReduceStock()
    {
        $client = $this->mockApiClient(Client::class, ['updateStock']);

        $client->expects()->updateStock('mock-card-id', 10, 'reduce')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->reduceStock('mock-card-id', 10));
    }

    public function testUpdateStock()
    {
        $client = $this->mockApiClient(Client::class)->shouldAllowMockingProtectedMethods()->makePartial();

        $client->expects()->httpPostJson('card/modifystock', [
            'card_id' => 'mock-card-id',
            'increase_stock_value' => 10,
        ])->andReturn('mock-result')->twice();

        $this->assertSame('mock-result', $client->updateStock('mock-card-id', 10));
        $this->assertSame('mock-result', $client->updateStock('mock-card-id', -10));

        $client->expects()->httpPostJson('card/modifystock', [
            'card_id' => 'mock-card-id',
            'reduce_stock_value' => 10,
        ])->andReturn('mock-result')->twice();

        $this->assertSame('mock-result', $client->updateStock('mock-card-id', 10, 'reduce'));
        $this->assertSame('mock-result', $client->updateStock('mock-card-id', -10, 'reduce'));
    }
}
