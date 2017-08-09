<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Payment;

use EasyWeChat\Payment\Application;
use EasyWeChat\Payment\BaseClient;
use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Kernel\Support;

class BaseClientTest extends TestCase
{
    public function testPrepends()
    {
        $app = new Application();
        $client = $this->mockApiClient(BaseClient::class, 'prepends', $app)->makePartial();

        $this->assertEmpty($client->prepends());
        $this->assertSame([], $client->prepends());
    }

    public function testRequest()
    {
        $app = new Application();
        $client = $this->mockApiClient(BaseClient::class, ['performRequest', 'resolveResponse', 'prepends', 'getSignKey'], $app)->makePartial();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar'];
        $method = 'post';
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
            ->once()
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
        $method = 'post';
        $options = [];

        $client->expects()->request($api, $params, $method, $options, true)->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->requestRaw($api, $params, $method, $options));
    }

    public function testSafeRequest()
    {
        $app = new Application([
            'app_id' => 'wx123456',
            'cert_path' => 'foo',
            'key_path' => 'bar',
        ]);
        $client = $this->mockApiClient(BaseClient::class, ['request', 'safeRequest'], $app)->makePartial();

        $api = 'http://easywechat.org';
        $params = ['foo' => 'bar'];
        $method = 'post';

        $options = [
            'cert' => $app['merchant']->get('cert_path'),
            'ssl_key' => $app['merchant']->get('key_path'),
        ];

        $client->expects()->request($api, $params, $method, $options)->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->safeRequest($api, $params, $method));
    }
}
