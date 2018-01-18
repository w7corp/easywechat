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

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OfficialAccount\Card\JssdkClient;
use EasyWeChat\Tests\TestCase;

class JssdkClientTest extends TestCase
{
    public function testGetTicket()
    {
        $app = new ServiceContainer([
            'app_id' => '123456',
        ]);
        $client = $this->mockApiClient(JssdkClient::class, ['getCache'], $app);
        $cache = \Mockery::mock(CacheInterface::class);
        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];
        $cacheKey = 'easywechat.basic_service.jssdk.ticket.wx_card.123456';
        $client->allows()->getCache()->andReturn($cache);

        $response = new \EasyWeChat\Kernel\Http\Response(200, [], json_encode($ticket));
        $cache->expects()->has($cacheKey)->andReturn(false);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500)->once();
        $client->expects()->requestRaw('https://api.weixin.qq.com/cgi-bin/ticket/getticket', 'GET', ['query' => ['type' => 'wx_card']])->andReturn($response)->once();

        $this->assertSame($ticket, $client->getTicket());
    }

    public function testAssign()
    {
        $client = $this->mockApiClient(JssdkClient::class, ['attachExtension']);

        $cards = [
            [
                'card_id' => 'mock-card-id1',
            ],
            [
                'card_id' => 'mock-card-id2',
            ],
        ];
        $client->expects()->attachExtension('mock-card-id1', [
            'card_id' => 'mock-card-id1',
        ])->andReturn([
            'card_id' => 'mock-card-id1',
            'assigned' => 'yes',
        ])->once();

        $client->expects()->attachExtension('mock-card-id2', [
            'card_id' => 'mock-card-id2',
        ])->andReturn([
            'card_id' => 'mock-card-id2',
            'assigned' => 'yes',
        ])->once();

        $this->assertSame(json_encode([
            [
                'card_id' => 'mock-card-id1',
                'assigned' => 'yes',
            ],
            [
                'card_id' => 'mock-card-id2',
                'assigned' => 'yes',
            ],
        ]), $client->assign($cards));
    }

    public function testAttachExtension()
    {
        $client = $this->mockApiClient(JssdkClient::class, ['dictionaryOrderSignature', 'getTicket']);

        $card = [
            'card_id' => 'mock-card-id1',
            'code' => 'mock-code',
            'openid' => 'mock-openid',
            'outer_id' => 'mock-outer_id',
            'balance' => 'mock-balance',
            'fixed_begintimestamp' => 'mock-fixed_begintimestamp',
            'outer_str' => 'mock-outer_str',
        ];

        $client->expects()->dictionaryOrderSignature('mock-ticket', \Mockery::type('int'), 'mock-card-id', 'mock-code', 'mock-openid', \Mockery::type('string'))
                    ->andReturn('mock-signature')->once();
        $client->expects()->getTicket()->andReturn(['ticket' => 'mock-ticket']);

        $attached = $client->attachExtension('mock-card-id', $card);
        $this->assertSame('mock-card-id', $attached['cardId']);
        $ext = json_decode($attached['cardExt'], true);

        $this->assertArrayHasKey('timestamp', $ext);
        $this->assertArraySubset([
            'code' => 'mock-code',
            'openid' => 'mock-openid',
            'outer_id' => 'mock-outer_id',
            'balance' => 'mock-balance',
            'fixed_begintimestamp' => 'mock-fixed_begintimestamp',
            'outer_str' => 'mock-outer_str',
            'signature' => 'mock-signature',
        ], $ext);
    }
}
