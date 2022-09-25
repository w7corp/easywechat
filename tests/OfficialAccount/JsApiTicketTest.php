<?php

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\OfficialAccount\AccessToken;
use EasyWeChat\OfficialAccount\JsApiTicket;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class JsApiTicketTest extends TestCase
{
    public function test_get_key()
    {
        $ticket = new JsApiTicket('mock-appid', 'mock-secret');

        $this->assertInstanceOf(AccessToken::class, $ticket);

        $this->assertSame('official_account.jsapi_ticket.mock-appid', $ticket->getKey());

        $ticket->setKey('mock-key');
        $this->assertSame('mock-key', $ticket->getKey());

        $ticket = new JsApiTicket('mock-appid', 'mock-secret', 'test-key');
        $this->assertSame('test-key', $ticket->getKey());
    }

    public function test_get_ticket()
    {
        $cacheKey = 'official_account.jsapi_ticket.mock-appid';

        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn($ticket);

        $client = \Mockery::mock(HttpClientInterface::class);

        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get($cacheKey)->andReturn($ticket['ticket']);

        $jsApiTicket = new JsApiTicket('mock-appid', 'mock-secret', null, $cache, $client);
        $this->assertSame($ticket['ticket'], $jsApiTicket->getTicket());

        //设为过期
        $cache->expects()->get($cacheKey)->andReturn(false);
        $cache->expects()->set($cacheKey, $ticket['ticket'], $ticket['expires_in'])->andReturn(true);

        $client->allows()->request('GET', '/cgi-bin/ticket/getticket', ['query' => ['type' => 'jsapi']])
            ->andReturn($response);

        $this->assertSame($ticket['ticket'], $jsApiTicket->getTicket());
    }

    public function test_config_signature()
    {
        $nonce = 'mock-nonce';
        $timestamp = 1601234567;

        $cacheKey = 'official_account.jsapi_ticket.mock-appid';

        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get($cacheKey)->andReturn('mock-ticket');

        $ticket = new JsApiTicket('mock-appid', 'mock-secret', null, $cache);

        $result = $ticket->configSignature('https://www.easywechat.com/', $nonce, $timestamp);

        $data = [
            'url' => 'https://www.easywechat.com/',
            'nonceStr' => 'mock-nonce',
            'timestamp' => 1601234567,
            'appId' => 'mock-appid',
            'signature' => '22772d2fb393ab9f7f6a5a54168a566fbf1ab767',
        ];

        $this->assertSame($data, $result);
    }
}
