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

use EasyWeChat\Kernel\Http\Response;
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
        $cacheKey = 'easywechat.work.jssdk.ticket.config.123456';
        $client->allows()->getCache()->andReturn($cache);
        $response = new \EasyWeChat\Kernel\Http\Response(200, [], json_encode($ticket));

        // no refresh and cached
        $cache->expects()->has($cacheKey)->andReturn(true);
        $cache->expects()->get($cacheKey)->andReturn($ticket);

        $this->assertSame($ticket, $client->getTicket());

        // no refresh and no cached
        $cache->expects()->has($cacheKey)->twice()->andReturn(false, true);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500);
        $client->expects()->requestRaw('https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket', 'GET')->andReturn($response);

        $this->assertSame($ticket, $client->getTicket());

        // with refresh and cached
        $cache->expects()->has($cacheKey)->andReturn(true);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500);
        $client->expects()->requestRaw('https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket', 'GET')->andReturn($response);

        $this->assertSame($ticket, $client->getTicket(true));
    }

    public function testGetAgentTicket()
    {
        $app = new ServiceContainer([
            'corp_id' => 'mock-corp-id',
        ]);

        $client = $this->mockApiClient(Client::class, ['getCache'], $app);
        $cache = \Mockery::mock(CacheInterface::class);

        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];
        $cacheKey = 'easywechat.work.jssdk.ticket.100023.agent_config.mock-corp-id';

        // no refresh and cached
        $cache->expects()->has($cacheKey)->andReturn(true);
        $cache->expects()->get($cacheKey)->andReturn($ticket);
        $client->allows()->getCache()->andReturn($cache);

        $this->assertSame($ticket, $client->getAgentTicket(100023));

        $response = new Response(200, [], json_encode($ticket));

        // no refresh and no cached
        $cache->expects()->has($cacheKey)->twice()->andReturn(false, true);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500);

        $client->expects()
            ->requestRaw('cgi-bin/ticket/get', 'GET', ['query' => ['type' => 'agent_config']])
            ->andReturn($response);

        $this->assertSame($ticket, $client->getAgentTicket(100023));

        // with refresh and cached
        $cache->expects()->has($cacheKey)->andReturn(true);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500);

        $client->expects()
            ->requestRaw('cgi-bin/ticket/get', 'GET', ['query' => ['type' => 'agent_config']])
            ->andReturn($response);

        $this->assertSame($ticket, $client->getAgentTicket(100023, true));
    }

    public function testBuildAgentConfig()
    {
        $client = $this->mockApiClient(\EasyWeChat\Work\Jssdk\Client::class, 'agentConfigSignature');
        $client->expects()->agentConfigSignature('agentId', null)->andReturn(['foo' => 'bar'])->twice();
        $config = json_decode($client->buildAgentConfig(['api1', 'api2'], 'agentId'), true);

        $this->assertArrayHasKey('debug', $config);
        $this->assertArrayHasKey('beta', $config);
        $this->assertArrayHasKey('jsApiList', $config);
        $this->assertArrayHasKey('foo', $config);
        $this->assertArrayHasKey('openTagList', $config);

        $this->assertFalse($config['debug']);
        $this->assertFalse($config['beta']);
        $this->assertSame(['api1', 'api2'], $config['jsApiList']);
        $this->assertSame('bar', $config['foo']);

        // beta: true, debug: true, json:false
        $config = $client->buildAgentConfig(['api1', 'api2'], 'agentId', true, true, false, ['foo', 'bar']);
        $this->assertArrayHasKey('debug', $config);
        $this->assertArrayHasKey('beta', $config);
        $this->assertArrayHasKey('jsApiList', $config);
        $this->assertArrayHasKey('foo', $config);
        $this->assertArrayHasKey('openTagList', $config);

        $this->assertTrue($config['debug']);
        $this->assertTrue($config['beta']);
        $this->assertSame(['api1', 'api2'], $config['jsApiList']);
        $this->assertSame('bar', $config['foo']);
        $this->assertSame(['foo', 'bar'], $config['openTagList']);
    }

    public function testGetAgentConfigArray()
    {
        $client = $this->mockApiClient(Client::class, 'buildAgentConfig');
        $client->expects()->buildAgentConfig(['api1', 'api2'], 'agentId', true, true, false, [], null)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAgentConfigArray(['api1', 'api2'], 'agentId', true, true));
    }
}
