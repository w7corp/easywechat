<?php

use EasyWeChat\Js\Js;

class JsJsTest extends TestCase
{
    public function getMockCache()
    {
        return Mockery::mock('EasyWeChat\Cache\Adapters\AdapterInterface');
    }

    public function getMockHttp()
    {
        return Mockery::mock(EasyWeChat\Core\http::class);
    }

    /**
     * Test config().
     */
    public function testConfig()
    {
        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $cache->shouldReceive('get')->andReturn('foo');

        $js = new Js('foo', 'bar', $cache, $http);
        $js->setUrl('http://easywechat.org');

        $config = $js->config(['startRecord', 'uploadImage']);

        $this->assertContains('"debug":false,"beta":false,"appId":"foo"', $config);
        $this->assertContains('"jsApiList":["startRecord","uploadImage"]', $config);

        $config = $js->config(['startRecord', 'uploadImage'], true);
        $this->assertContains('"debug":true,"beta":false,"appId":"foo"', $config);

        $config = $js->config(['startRecord', 'uploadImage'], true, true);
        $this->assertContains('"debug":true,"beta":true,"appId":"foo"', $config);

        $config = $js->config(['startRecord', 'uploadImage'], true, true, false);

        $this->assertEquals(['startRecord', 'uploadImage'], $config['jsApiList']);
    }

    /**
     * Test getConfigArray().
     */
    public function testGetConfigArray()
    {
        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $cache->shouldReceive('get')->andReturn('foo');

        $js = new Js('foo', 'bar', $cache, $http);
        $js->setUrl('http://easywechat.org');

        $config = $js->getConfigArray(['startRecord', 'uploadImage']);

        $this->assertEquals(['startRecord', 'uploadImage'], $config['jsApiList']);
    }

    /**
     * Test ticket().
     */
    public function testTicket()
    {
        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $cache->shouldReceive('get')->andReturn('foo');

        $js = new Js('foo', 'bar', $cache, $http);
        $js->setUrl('http://easywechat.org');

        $this->assertEquals('foo', $js->ticket());

        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $cache->shouldReceive('get')->andReturnUsing(function ($key, $callback) {
            return $callback($key);
        });
        $cache->shouldReceive('set')->andReturnUsing(function ($key, $ticket, $expires) {
            return $ticket;
        });
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturn(['ticket' => 'overtrue.ticket', 'expires_in' => 7200]);

        $js = new Js('foo', 'bar', $cache, $http);

        $this->assertEquals('overtrue.ticket', $js->ticket());
    }

    /**
     * Test signature().
     */
    public function testSignature()
    {
        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $cache->shouldReceive('get')->andReturn('foo');

        $js = new Js('foo', 'bar', $cache, $http);
        $js->setUrl('http://easywechat.org');

        $signaturePack = $js->signature();

        $this->assertEquals('http://easywechat.org', $signaturePack['url']);
        $this->assertEquals('foo', $signaturePack['appId']);
        $this->assertArrayHasKey('signature', $signaturePack);
        $this->assertArrayHasKey('nonceStr', $signaturePack);
        $this->assertArrayHasKey('timestamp', $signaturePack);

        $signaturePack = $js->signature('http://overtrue.me');
        $this->assertEquals('http://overtrue.me', $signaturePack['url']);

        $signaturePack = $js->signature('http://overtrue.me', 'foobar');
        $this->assertEquals('http://overtrue.me', $signaturePack['url']);
        $this->assertEquals('foobar', $signaturePack['nonceStr']);

        $signaturePack = $js->signature('http://overtrue.me', 'foobar', 1234578902);
        $this->assertEquals('http://overtrue.me', $signaturePack['url']);
        $this->assertEquals('foobar', $signaturePack['nonceStr']);
        $this->assertEquals('1234578902', $signaturePack['timestamp']);
    }

    /**
     * Test getSignature().
     */
    public function testGetSignature()
    {
        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $http->shouldReceive('setExpectedException')->andReturn($http);

        $js = new Js('foo', 'bar', $cache, $http);
        $js->setUrl('http://easywechat.org');

        $ticket = 'ticket';
        $nonce = 'foo';
        $timestamp = 1234578902;
        $url = 'http://easywechat.org';
        $expect = sha1("jsapi_ticket={$ticket}&noncestr={$nonce}&timestamp={$timestamp}&url={$url}");

        $this->assertEquals($expect, $js->getSignature($ticket, $nonce, $timestamp, $url));
    }

    /**
     * Test setUrl().
     */
    public function testSetUrl()
    {
        $http = $this->getMockHttp();
        $cache = $this->getMockCache();
        $http->shouldReceive('setExpectedException')->andReturn($http);

        $js = new Js('foo', 'bar', $cache, $http);
        $js->setUrl('http://easywechat.org');

        $this->assertEquals('http://easywechat.org', $js->getUrl());
    }
}
