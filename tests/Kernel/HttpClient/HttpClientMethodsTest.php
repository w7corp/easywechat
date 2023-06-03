<?php

namespace EasyWeChat\Tests\Kernel\HttpClient;

use EasyWeChat\Kernel\HttpClient\HttpClientMethods;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClientMethodsTest extends TestCase
{
    public function test_get()
    {
        $client = new DummyHttpClient();

        $response = $client->get('http://easywechat.com');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame('GET', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
    }

    public function test_post()
    {
        $client = new DummyHttpClient();

        $response = $client->post('http://easywechat.com', ['foo' => 'bar']);
        $this->assertSame('POST', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['foo' => 'bar'], $response->getRequestOptions()['body']);

        $response = $client->post('http://easywechat.com', ['json' => ['foo' => 'bar']]);
        $this->assertSame('POST', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['json' => ['foo' => 'bar']], $response->getRequestOptions());

        $response = $client->post('http://easywechat.com', ['xml' => ['foo' => 'bar']]);
        $this->assertSame('POST', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['xml' => ['foo' => 'bar']], $response->getRequestOptions());
    }

    public function test_post_json()
    {
        $client = new DummyHttpClient();

        $response = $client->postJson('http://easywechat.com', ['foo' => 'bar']);

        $this->assertSame('POST', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['foo' => 'bar'], $response->getRequestOptions()['json']);
        $this->assertSame('application/json', $response->getRequestOptions()['headers']['Content-Type']);
    }

    public function test_post_xml()
    {
        $client = new DummyHttpClient();

        // no type
        $response = $client->postXml('http://easywechat.com', ['foo' => 'bar', 'headers' => ['Accept' => 'application/xml']]);

        $this->assertSame('POST', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['foo' => 'bar'], $response->getRequestOptions()['xml']);
        $this->assertSame('text/xml', $response->getRequestOptions()['headers']['Content-Type']);
        $this->assertSame('application/xml', $response->getRequestOptions()['headers']['Accept']);

        // with type
        $response = $client->postXml('http://easywechat.com', ['xml' => ['foo' => 'bar']]);

        $this->assertSame('POST', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['foo' => 'bar'], $response->getRequestOptions()['xml']);
        $this->assertSame('text/xml', $response->getRequestOptions()['headers']['Content-Type']);

        // with string
        $response = $client->postXml('http://easywechat.com', ['xml' => Xml::build(['foo' => 'bar'])]);

        $this->assertSame('POST', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(Xml::build(['foo' => 'bar']), $response->getRequestOptions()['xml']);
        $this->assertSame('text/xml', $response->getRequestOptions()['headers']['Content-Type']);
    }

    public function test_put()
    {
        $client = new DummyHttpClient();

        $response = $client->put('http://easywechat.com', ['foo' => 'bar']);
        $this->assertSame('PUT', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['foo' => 'bar'], $response->getRequestOptions()['body']);
    }

    public function test_patch()
    {
        $client = new DummyHttpClient();

        $response = $client->patch('http://easywechat.com', ['foo' => 'bar']);
        $this->assertSame('PATCH', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['foo' => 'bar'], $response->getRequestOptions()['body']);
    }

    public function test_patch_json()
    {
        $client = new DummyHttpClient();

        $response = $client->patchJson('http://easywechat.com', ['foo' => 'bar']);
        $this->assertSame('PATCH', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
        $this->assertSame(['foo' => 'bar'], $response->getRequestOptions()['json']);
        $this->assertSame('application/json', $response->getRequestOptions()['headers']['Content-Type']);
    }

    public function test_delete()
    {
        $client = new DummyHttpClient();

        $response = $client->delete('http://easywechat.com');
        $this->assertSame('DELETE', $response->getRequestMethod());
        $this->assertSame('http://easywechat.com', $response->getRequestUrl());
    }
}

class DummyHttpClient
{
    use HttpClientMethods;

    public function request($method, $url, $options = []): ResponseInterface
    {
        $response = new MockResponse('');

        $response->fromRequest($method, $url, $options, $response);

        return $response;
    }
}
