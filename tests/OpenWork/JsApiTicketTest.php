<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\OpenWork\JsApiTicket;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class JsApiTicketTest extends TestCase
{
    public function test_get_key()
    {
        $ticket = new JsApiTicket('mock-corpid');

        $this->assertSame('open_work.jsapi_ticket.mock-corpid', $ticket->getKey());

        $ticket->setKey('mock-key');
        $this->assertSame('mock-key', $ticket->getKey());

        $ticket = new JsApiTicket('mock-corpid', 'test-key');
        $this->assertSame('test-key', $ticket->getKey());
    }

    public function test_get_agent_key()
    {
        $ticket = new JsApiTicket('mock-corpid');

        $this->assertSame('open_work.jsapi_ticket.mock-corpid.100001', $ticket->getAgentKey(100001));

        $ticket->setKey('mock-key');
        $this->assertSame('mock-key.100001', $ticket->getAgentKey(100001));

        $ticket = new JsApiTicket('mock-corpid', 'test-key');
        $this->assertSame('test-key.100001', $ticket->getAgentKey(100001));
    }

    public function test_get_ticket()
    {
        $cacheKey = 'open_work.jsapi_ticket.mock-corpid';
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

        $cache->expects()->get($cacheKey)->andReturn(false);
        $cache->expects()->set($cacheKey, $ticket['ticket'], $ticket['expires_in'])->andReturn(true);

        $client->allows()->request('GET', '/cgi-bin/get_jsapi_ticket')->andReturn($response);

        $this->assertSame($ticket['ticket'], $jsApiTicket->getTicket());
    }

    public function test_get_agent_ticket()
    {
        $cacheKey = 'open_work.jsapi_ticket.mock-corpid.100001';
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

        $cache->expects()->get($cacheKey)->andReturn(false);
        $cache->expects()->set($cacheKey, $ticket['ticket'], $ticket['expires_in'])->andReturn(true);

        $client->allows()->request('GET', '/cgi-bin/ticket/get', ['query' => ['type' => 'agent_config']])->andReturn($response);

        $this->assertSame($ticket['ticket'], $jsApiTicket->getAgentTicket(100001));
    }

    public function test_get_ticket_without_expires_in_does_not_trigger_warnings()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-key')->andReturn(false);
        $cache->expects()->set('mock-key', 'mock-ticket', 0)->andReturn(true);

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'ticket' => 'mock-ticket',
        ]);

        $client = \Mockery::mock(HttpClientInterface::class);
        $client->allows()->request('GET', '/cgi-bin/get_jsapi_ticket')->andReturn($response);

        $errors = [];
        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $ticket = new JsApiTicket('mock-corpid', 'mock-key', $cache, $client);

            $this->assertSame('mock-ticket', $ticket->getTicket());
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }

    public function test_get_agent_ticket_without_expires_in_does_not_trigger_warnings()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-key.100001')->andReturn(false);
        $cache->expects()->set('mock-key.100001', 'mock-ticket', 0)->andReturn(true);

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'ticket' => 'mock-ticket',
        ]);

        $client = \Mockery::mock(HttpClientInterface::class);
        $client->allows()->request('GET', '/cgi-bin/ticket/get', ['query' => ['type' => 'agent_config']])->andReturn($response);

        $errors = [];
        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $ticket = new JsApiTicket('mock-corpid', 'mock-key', $cache, $client);

            $this->assertSame('mock-ticket', $ticket->getAgentTicket(100001));
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }

    public function test_config_signature()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('open_work.jsapi_ticket.mock-corpid')->andReturn('mock-ticket');

        $ticket = new JsApiTicket('mock-corpid', cache: $cache);

        $this->assertSame([
            'appId' => 'mock-corpid',
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'url' => 'https://www.easywechat.com/',
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
            'jsApiList' => [],
            'debug' => false,
            'beta' => true,
        ], $ticket->createConfigSignature('mock-nonce', 1601234567, 'https://www.easywechat.com/'));
    }

    public function test_agent_config_signature()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('open_work.jsapi_ticket.mock-corpid.100001')->andReturn('mock-ticket');

        $ticket = new JsApiTicket('mock-corpid', cache: $cache);

        $this->assertSame([
            'corpid' => 'mock-corpid',
            'agentid' => 100001,
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
            'jsApiList' => [],
        ], $ticket->createAgentConfigSignature(100001, 'mock-nonce', 1601234567, 'https://www.easywechat.com/'));
    }
}
