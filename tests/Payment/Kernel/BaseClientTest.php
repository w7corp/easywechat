<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment\Kernel;

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support;
use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\Kernel\BaseClient;
use EasyWeChat\Tests\TestCase;

class BaseClientTest extends TestCase
{
    public function testRequest()
    {
        $app = new Application();

        $client = $this->mockApiClient(BaseClient::class, ['performRequest', 'resolveResponse'], $app)->shouldDeferMissing();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar'];
        $method = \Mockery::anyOf(['get', 'post']);
        $options = ['foo' => 'bar'];

        $mockResponse = new Response(200, [], 'response-content');

        $client->expects()->performRequest($api, $method, \Mockery::on(function ($options) {
            $this->assertSame('bar', $options['foo']);
            $this->assertInternalType('string', $options['body']);

            $bodyInOptions = Support\XML::parse($options['body']);

            $this->assertSame($bodyInOptions['foo'], $options['foo']);
            $this->assertInternalType('string', $bodyInOptions['nonce_str']);
            $this->assertInternalType('string', $bodyInOptions['sign']);

            return true;
        }))->times(3)->andReturn($mockResponse);

        $client->expects()->resolveResponse()
            ->with($mockResponse, \Mockery::any())
            ->andReturn(['foo' => 'mock-bar']);

        // $returnResponse = false
        $this->assertSame(['foo' => 'mock-bar'], $client->request($api, $params, $method, $options, false));

        // $returnResponse = true
        $this->assertInstanceOf(Response::class, $client->request($api, $params, $method, $options, true));
        $this->assertSame('response-content', $client->request($api, $params, $method, $options, true)->getBodyContents());
    }

    public function testRequestRaw()
    {
        $app = new Application();

        $client = $this->mockApiClient(BaseClient::class, ['request', 'requestRaw'], $app)->makePartial();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar'];
        $method = \Mockery::anyOf(['get', 'post']);
        $options = [];

        $client->expects()->request($api, $params, $method, $options, true)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->requestRaw($api, $params, $method, $options));
    }

    public function testSafeRequest()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'cert_path' => 'foo',
            'key_path' => 'bar',
        ]);

        $client = $this->mockApiClient(BaseClient::class, ['safeRequest'], $app)->makePartial();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar'];
        $method = \Mockery::anyOf(['get', 'post']);

        $client->expects()->request($api, $params, $method, \Mockery::on(function ($options) use ($app) {
            $this->assertSame($options['cert'], $app['config']->get('cert_path'));
            $this->assertSame($options['ssl_key'], $app['config']->get('key_path'));

            return true;
        }))->andReturn('mock-result');

        $this->assertSame('mock-result', $client->safeRequest($api, $params, $method));
    }

    public function testGetKey()
    {
        $app = new Application(['key' => 'mock-key']);
        $client = $this->mockApiClient(BaseClient::class, ['getKey'], $app)->shouldAllowMockingProtectedMethods();
        $client->expects()->getKey('foobar')->passthru();
        $this->assertSame('mock-key', $client->getKey('foobar'));
    }

    public function testGetKeyInSandboxMode()
    {
        $app = new Application([
            'sandbox' => true,
        ]);
        $sandbox = \Mockery::mock(\EasyWeChat\Payment\Sandbox\Client::class.'[except,key]', new ServiceContainer());
        $sandbox->expects()->except('foobar')->andReturn(false);
        $sandbox->expects()->key()->andReturn('123');
        $app['sandbox'] = $sandbox;

        $client = $this->mockApiClient(BaseClient::class, ['getKey'], $app)->shouldAllowMockingProtectedMethods();
        $client->expects()->getKey('foobar')->passthru();
        $this->assertSame('123', $client->getKey('foobar'));
    }
}
