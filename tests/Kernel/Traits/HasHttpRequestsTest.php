<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Traits;

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Kernel\Traits\HasHttpRequests;
use EasyWeChat\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;

class HasHttpRequestsTest extends TestCase
{
    public function testDefaultOptions()
    {
        $this->assertSame([
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
        ], HasHttpRequests::getDefaultOptions());

        HasHttpRequests::setDefaultOptions(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], HasHttpRequests::getDefaultOptions());
    }

    public function testHttpClient()
    {
        $cls = \Mockery::mock(HasHttpRequests::class);
        $this->assertInstanceOf(ClientInterface::class, $cls->getHttpClient());

        $client = \Mockery::mock(Client::class);
        $cls->setHttpClient($client);
        $this->assertSame($client, $cls->getHttpClient());
    }

    public function testMiddlewareFeatures()
    {
        $cls = \Mockery::mock(HasHttpRequests::class);
        $this->assertEmpty($cls->getMiddlewares());

        $fn1 = function () {
        };
        $fn2 = function () {
        };
        $fn3 = function () {
        };

        $cls->pushMiddleware($fn1, 'fn1');
        $cls->pushMiddleware($fn2, 'fn2');
        $cls->pushMiddleware($fn3);

        $this->assertSame(['fn1' => $fn1, 'fn2' => $fn2, $fn3], $cls->getMiddlewares());
    }

    public function testRequest()
    {
        $cls = \Mockery::mock(DummnyClassForHasHttpRequestTest::class.'[getHandlerStack]');
        $handlerStack = \Mockery::mock(HandlerStack::class);
        $cls->allows()->getHandlerStack()->andReturn($handlerStack);

        $client = \Mockery::mock(Client::class);
        $cls->setHttpClient($client);

        $response = new Response(200, [], 'mock-result');

        // default arguments
        $client->expects()->request('GET', 'foo/bar', [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'handler' => $handlerStack,
            'base_uri' => 'http://easywechat.com',
        ])->andReturn($response)->once();

        $this->assertSame($response, $cls->request('foo/bar'));

        // custom arguments
        $client->expects()->request('POST', 'foo/bar', [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'query' => ['foo' => 'bar'],
            'handler' => $handlerStack,
            'base_uri' => 'http://easywechat.com',
        ])->andReturn($response)->once();

        $this->assertSame($response, $cls->request('foo/bar', 'post', ['query' => ['foo' => 'bar']]));
    }

    public function testHandlerStack()
    {
        $cls = \Mockery::mock(HasHttpRequests::class);
        $fn1 = function () {
        };
        $cls->pushMiddleware($fn1, 'fn1');

        $handlerStack = $cls->getHandlerStack();
        $this->assertInstanceOf(HandlerStack::class, $handlerStack);
        $this->assertContains('Name: \'fn1\', Function: callable', (string) $handlerStack);

        $handlerStack2 = \Mockery::mock(HandlerStack::class);
        $cls->setHandlerStack($handlerStack2);
        $this->assertSame($handlerStack2, $cls->getHandlerStack());
    }

    public function testResolveResponse()
    {
        $cls = \Mockery::mock(DummnyClassForHasHttpRequestTest::class)->makePartial();

        $response = new Response(200, [], '{"foo": "bar"}');

        // collection
        $collection = $cls->resolveResponse($response, 'collection');
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertSame(['foo' => 'bar'], $collection->all());

        // array
        $this->assertSame(['foo' => 'bar'], $cls->resolveResponse($response, 'array'));

        // object
        $this->assertSame('bar', $cls->resolveResponse($response, 'object')->foo);

        // raw
        $raw = $cls->resolveResponse($response, 'raw');
        $this->assertInstanceOf(Response::class, $raw);

        // custom class
        // 1. exists
        $dummyResponse = $cls->resolveResponse($response, DummyResponseClassForHasHttpRequestTest::class);
        $this->assertInstanceOf(DummyResponseClassForHasHttpRequestTest::class, $dummyResponse);
        $this->assertInstanceOf(Response::class, $dummyResponse->response);

        // 2. not exists
        $resp = $cls->resolveResponse($response, 'Not\Exists\ClassName');
        $this->assertInstanceOf(Response::class, $resp);
        $this->assertSame('{"foo": "bar"}', $resp->getBody()->getContents());
    }

    public function testTransformResponseToType()
    {
        $cls = \Mockery::mock(DummnyClassForHasHttpRequestTest::class)->makePartial();

        // response
        $response = new Response(200, [], '{"foo": "bar"}');
        $this->assertInstanceOf(Collection::class, $cls->transformResponseToType($response, 'collection'));

        // array
        $response = ['foo' => 'bar'];
        $this->assertInstanceOf(Collection::class, $cls->transformResponseToType($response, 'collection'));
        $this->assertSame(['foo' => 'bar'], $cls->transformResponseToType($response, 'collection')->all());

        // object
        $response = json_decode(json_encode(['foo' => 'bar']));
        $this->assertSame(['foo' => 'bar'], $cls->transformResponseToType($response, 'array'));
    }

    public function testFixJsonIssue()
    {
        $cls = \Mockery::mock(DummnyClassForHasHttpRequestTest::class.'[getHandlerStack]');
        $handlerStack = \Mockery::mock(HandlerStack::class);
        $cls->allows()->getHandlerStack()->andReturn($handlerStack);

        $client = \Mockery::mock(Client::class);
        $cls->setHttpClient($client);

        $response = new Response(200, [], 'mock-result');

        // default arguments
        $client->expects()->request('POST', 'foo/bar', [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'handler' => $handlerStack,
            'body' => '{}',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'base_uri' => 'http://easywechat.com',
        ])->andReturn($response)->once();

        $this->assertSame($response, $cls->request('foo/bar', 'POST', [
            'json' => [],
        ]));

        // unescape unicode
        $client->expects()->request('POST', 'foo/bar', [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
            'handler' => $handlerStack,
            'body' => '{"name":"中文"}',
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'base_uri' => 'http://easywechat.com',
        ])->andReturn($response)->once();

        $cls->request('foo/bar', 'POST', [
            'json' => ['name' => '中文'],
        ]);
    }
}

class DummyResponseClassForHasHttpRequestTest
{
    public $response;

    public function __construct($response)
    {
        $this->response = $response;
    }
}

class DummnyClassForHasHttpRequestTest
{
    use HasHttpRequests;

    protected $baseUri = 'http://easywechat.com';
}
