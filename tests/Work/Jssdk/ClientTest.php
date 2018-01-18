<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Jssdk;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Jssdk\Client;

class ClientTest extends TestCase
{
    public function testGetAppid()
    {
        $client = $this->mockApiClient(Client::class, ['getAppId'], new Application(['corp_id' => 'wx123']))->shouldAllowMockingProtectedMethods();
        $client->expects()->getAppId()->passthru();
        $this->assertSame('wx123', $client->getAppId());
    }

    public function testGetTicket()
    {
        $app = new ServiceContainer([
            'corp_id' => '123456',
        ]);
        $client = $this->mockApiClient(Client::class, ['getCache'], $app);
        $cache = \Mockery::mock(CacheInterface::class);
        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];
        $cacheKey = 'easywechat.basic_service.jssdk.ticket.jsapi.123456';
        $client->allows()->getCache()->andReturn($cache);

        // no refresh and cached
        $cache->expects()->has($cacheKey)->andReturn(true);
        $cache->expects()->get($cacheKey)->andReturn($ticket);

        $this->assertSame($ticket, $client->getTicket());

        // no refresh and no cached
        $response = new \EasyWeChat\Kernel\Http\Response(200, [], json_encode($ticket));

        $cache->expects()->has($cacheKey)->andReturn(false);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500)->once();
        $client->expects()->requestRaw('https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket', 'GET', ['query' => ['type' => 'jsapi']])->andReturn($response)->once();

        $this->assertSame($ticket, $client->getTicket());

        // with refresh and cached
        $cache->expects()->has('mock-cache-key')->never();
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500)->once();
        $client->expects()->requestRaw('https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket', 'GET', ['query' => ['type' => 'jsapi']])->andReturn($response)->once();

        $this->assertSame($ticket, $client->getTicket(true));
    }
}
