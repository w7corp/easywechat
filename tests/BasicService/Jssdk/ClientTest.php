<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\BasicService\Jssdk;

use EasyWeChat\BasicService\Jssdk\Client;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testBuildConfig()
    {
        $client = $this->mockApiClient(Client::class, 'configSignature');
        $client->expects()->configSignature('https://www.easywechat.com/docs/4.1/basic-services/jssdk')->andReturn(['foo' => 'bar'])->twice();
        $config = json_decode($client->buildConfig(['api1', 'api2'], false, false, true, [], 'https://www.easywechat.com/docs/4.1/basic-services/jssdk'), true);

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
        $config = $client->buildConfig(['api1', 'api2'], true, true, false, ['foo', 'bar'], 'https://www.easywechat.com/docs/4.1/basic-services/jssdk');
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

    public function testGetConfigArray()
    {
        $client = $this->mockApiClient(Client::class, 'buildConfig');
        $client->expects()->buildConfig(['api1', 'api2'], true, true, false, [], 'https://www.easywechat.com/docs/4.1/basic-services/jssdk')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getConfigArray(['api1', 'api2'], true, true, [], 'https://www.easywechat.com/docs/4.1/basic-services/jssdk'));
    }

    public function testGetTicket()
    {
        $app = new ServiceContainer([
            'app_id' => '123456',
        ]);
        $client = $this->mockApiClient(Client::class, ['getCache'], $app);
        $cache = \Mockery::mock(CacheInterface::class);
        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];
        $cacheKey = 'easywechat.basic_service.jssdk.ticket.jsapi.123456';
        $client->allows()->getCache()->andReturn($cache);
        $response = new \EasyWeChat\Kernel\Http\Response(200, [], json_encode($ticket));

        // no refresh and cached
        $cache->expects()->has($cacheKey)->andReturn(true);
        $cache->expects()->get($cacheKey)->andReturn($ticket);

        $this->assertSame($ticket, $client->getTicket());

        // no refresh and no cached
        $cache->expects()->has($cacheKey)->twice()->andReturns(false, true);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500);
        $client->expects()->requestRaw('/cgi-bin/ticket/getticket', 'GET', ['query' => ['type' => 'jsapi']])->andReturn($response);

        $this->assertSame($ticket, $client->getTicket());

        // with refresh and cached
        $cache->expects()->has($cacheKey)->andReturn(true);
        $cache->expects()->get($cacheKey)->never();
        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500);
        $client->expects()->requestRaw('/cgi-bin/ticket/getticket', 'GET', ['query' => ['type' => 'jsapi']])->andReturn($response);

        $this->assertSame($ticket, $client->getTicket(true));

        // cache failed
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to cache jssdk ticket.');

        $cache->expects()->set($cacheKey, $ticket, $ticket['expires_in'] - 500);
        $cache->expects()->has($cacheKey)->andReturn(false);
        $client->expects()->requestRaw('/cgi-bin/ticket/getticket', 'GET', ['query' => ['type' => 'jsapi']])->andReturn($response);

        $client->getTicket(true);
    }

    public function testSignature()
    {
        $app = new ServiceContainer([
            'app_id' => '123456',
        ]);
        $url = 'http://easywechat.com';
        $ticket = [
            'ticket' => 'mock-ticket',
            'expires_in' => 7200,
        ];
        $client = $this->mockApiClient(Client::class, ['getUrl', 'getTicket', 'getTicketSignature'], $app)->makePartial();
        $client->allows()->getUrl()->andReturn($url)->once();
        $client->allows()->getTicketSignature('mock-ticket', \Mockery::type('string'), \Mockery::type('integer'), $url)
                            ->andReturn('mock-signature');
        $client->allows()->getTicket()->andReturn($ticket);
        $signature = $client->configSignature();

        $this->assertArrayHasKey('appId', $signature);
        $this->assertArrayHasKey('nonceStr', $signature);
        $this->assertArrayHasKey('timestamp', $signature);
        $this->assertArrayHasKey('url', $signature);
        $this->assertArrayHasKey('signature', $signature);

        $this->assertSame('123456', $signature['appId']);
        $this->assertSame(10, strlen($signature['nonceStr']), 'nonceStr length is 10');
        $this->assertSame($url, $signature['url']);
        $this->assertInternalType('integer', $signature['timestamp']);

        // custom arguments
        $time = time();
        $signature = $client->configSignature('http://easywechat.org', 'mock-nonce', $time);

        $this->assertArrayHasKey('appId', $signature);
        $this->assertArrayHasKey('nonceStr', $signature);
        $this->assertArrayHasKey('timestamp', $signature);
        $this->assertArrayHasKey('url', $signature);
        $this->assertArrayHasKey('signature', $signature);

        $this->assertSame('123456', $signature['appId']);
        $this->assertSame('mock-nonce', $signature['nonceStr'], 'nonceStr length is 10');
        $this->assertSame('http://easywechat.org', $signature['url']);
        $this->assertSame($time, $signature['timestamp']);
    }

    public function testGetTicketSignature()
    {
        $client = $this->mockApiClient(Client::class);
        $ticket = 'ticket';
        $nonce = 'nonce';
        $timestamp = time();
        $url = 'http://easywechat.com';
        $this->assertSame(
            sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}"),
            $client->getTicketSignature($ticket, $nonce, $timestamp, $url)
        );
    }

    public function testDictionaryOrderSignature()
    {
        $client = $this->mockApiClient(Client::class);

        $params = $unsorted = ['a', 'b', 1, 11];

        sort($params, SORT_STRING);

        $this->assertSame(sha1(implode('', $params)), $client->dictionaryOrderSignature(...$unsorted));
    }

    public function testUrlSetterAndGetter()
    {
        $client = $this->mockApiClient(Client::class);
        $_SERVER['HTTP_HOST'] = 'easywechat.com';
        $_SERVER['REQUEST_URI'] = '/foo/bar?appid=1234';

        $this->assertSame('http://easywechat.com/foo/bar?appid=1234', $client->getUrl());

        $newUrl = 'http://easywechat.org/another/path?foo=bar';
        $client->setUrl($newUrl);
        $this->assertSame($newUrl, $client->getUrl());
    }
}
