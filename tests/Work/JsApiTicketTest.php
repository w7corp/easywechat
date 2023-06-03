<?php

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\JsApiTicket;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class JsApiTicketTest extends TestCase
{
    public function test_get_key()
    {
        $ticket = new JsApiTicket('mock-corpid');

        $this->assertInstanceOf(JsApiTicket::class, $ticket);

        $this->assertSame('work.jsapi_ticket.mock-corpid', $ticket->getKey());

        $ticket->setKey('mock-key');
        $this->assertSame('mock-key', $ticket->getKey());

        $ticket = new JsApiTicket('mock-corpid', 'test-key');
        $this->assertSame('test-key', $ticket->getKey());
    }

    public function test_get_agent_key()
    {
        $ticket = new JsApiTicket('mock-corpid');

        $this->assertInstanceOf(JsApiTicket::class, $ticket);

        $this->assertSame('work.jsapi_ticket.mock-corpid.100001', $ticket->getAgentKey(100001));

        $ticket->setKey('mock-key');
        $this->assertSame('mock-key.100001', $ticket->getAgentKey(100001));

        $ticket = new JsApiTicket('mock-corpid', 'test-key');
        $this->assertSame('test-key.100001', $ticket->getAgentKey(100001));
    }

    public function test_get_ticket()
    {
        $cacheKey = 'work.jsapi_ticket.mock-corpid';

        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn($ticket);

        $client = \Mockery::mock(HttpClientInterface::class);

        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get($cacheKey)->andReturn($ticket['ticket']);

        $jsApiTicket = new JsApiTicket('mock-corpid', cache: $cache, httpClient: $client);
        $this->assertSame($ticket['ticket'], $jsApiTicket->getTicket());

        //设为过期
        $cache->expects()->get($cacheKey)->andReturn(false);
        $cache->expects()->set($cacheKey, $ticket['ticket'], $ticket['expires_in'])->andReturn(true);

        $client->allows()->request('GET', '/cgi-bin/get_jsapi_ticket')
            ->andReturn($response);

        $this->assertSame($ticket['ticket'], $jsApiTicket->getTicket());
    }

    public function test_get_agent_ticket()
    {
        $cacheKey = 'work.jsapi_ticket.mock-corpid.100001';

        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn($ticket);

        $client = \Mockery::mock(HttpClientInterface::class);

        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get($cacheKey)->andReturn($ticket['ticket']);

        $jsApiTicket = new JsApiTicket('mock-corpid', cache: $cache, httpClient: $client);

        $this->assertSame($ticket['ticket'], $jsApiTicket->getAgentTicket(100001));

        //设为过期
        $cache->expects()->get($cacheKey)->andReturn(false);
        $cache->expects()->set($cacheKey, $ticket['ticket'], $ticket['expires_in'])->andReturn(true);

        $client->allows()->request('GET', '/cgi-bin/ticket/get', ['query' => ['type' => 'agent_config']])
            ->andReturn($response);

        $this->assertSame($ticket['ticket'], $jsApiTicket->getAgentTicket(100001));
    }

    public function test_config_signature()
    {
        $nonce = 'mock-nonce';
        $timestamp = 1601234567;

        $cacheKey = 'work.jsapi_ticket.mock-corpid';

        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get($cacheKey)->andReturn('mock-ticket');

        $ticket = new JsApiTicket('mock-corpid', cache: $cache);

        $result = $ticket->createConfigSignature('https://www.easywechat.com/', $nonce, $timestamp);

        $data = [
            'appId' => 'mock-corpid',
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'url' => 'https://www.easywechat.com/',
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
        ];

        $this->assertSame($data, $result);
    }

    public function test_agent_config_signature()
    {
        $nonce = 'mock-nonce';
        $timestamp = 1601234567;

        $cacheKey = 'work.jsapi_ticket.mock-corpid.100001';

        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get($cacheKey)->andReturn('mock-ticket');

        $ticket = new JsApiTicket('mock-corpid', cache: $cache);

        $result = $ticket->createAgentConfigSignature(100001, 'https://www.easywechat.com/', $nonce, $timestamp);

        $data = [
            'corpid' => 'mock-corpid',
            'agentid' => 100001,
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'url' => 'https://www.easywechat.com/',
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
        ];

        $this->assertSame($data, $result);
    }

    public function test_get_ticket_signature()
    {
        $ticket = new JsApiTicket('mock-corpid', 'mock-secret');

        $sign = $ticket->getTicketSignature('mock-ticket', 'mock-nonce', 1601234567, 'https://www.easywechat.com/');
        $this->assertSame('22772d2fb393ab9f7f6a5a54168a566fbf1ab767', $sign);
    }
}
