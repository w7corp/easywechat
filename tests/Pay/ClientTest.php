<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Pay\Client;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class ClientTest extends TestCase
{
    public function test_v3_request()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->andReturn('mock-signature');

        $options = [
            'headers' => [
                'accept' => 'application/json',
            ],
        ];

        $client->request('GET', 'https://api2.mch.weixin.qq.com/v3/certificates', $options);
        $this->assertSame('GET', $client->getRequestMethod());
        $this->assertSame('https://api2.mch.weixin.qq.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: application/json', $client->getRequestOptions()['headers'][3]);
        $this->assertSame('accept: application/json', $client->getRequestOptions()['headers'][0]);
    }

    public function test_v2_request_with_array()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->never();
        $client->shouldReceive('attachLegacySignature')->with([
            'foo' => 'bar',
        ])->andReturn(['foo' => 'bar', 'sign' => 'mock-signature']);

        $client->post('certificates', [
            'body' => [
                'foo' => 'bar',
            ],
        ]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://api.mch.weixin.qq.com/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: text/xml', $client->getRequestOptions()['headers'][1]);
        $this->assertSame(Xml::build(['foo' => 'bar', 'sign' => 'mock-signature']), $client->getRequestOptions()['body']);
    }

    public function test_v2_request_without_body()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->never();
        $client->shouldReceive('attachLegacySignature')->with([
            'foo' => 'bar',
        ])->andReturn(['foo' => 'bar', 'sign' => 'mock-signature']);

        $client->post('certificates', ['foo' => 'bar']);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://api.mch.weixin.qq.com/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: text/xml', $client->getRequestOptions()['headers'][1]);
        $this->assertSame(Xml::build(['foo' => 'bar', 'sign' => 'mock-signature']), $client->getRequestOptions()['body']);
    }

    public function test_v2_request_with_xml_option()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->never();
        $client->shouldReceive('attachLegacySignature')->with([
            'foo' => 'bar',
        ])->andReturn(['foo' => 'bar', 'sign' => 'mock-signature']);

        $client->post('certificates', ['xml' => ['foo' => 'bar']]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://api.mch.weixin.qq.com/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: text/xml', $client->getRequestOptions()['headers'][1]);
        $this->assertSame(Xml::build(['foo' => 'bar', 'sign' => 'mock-signature']), $client->getRequestOptions()['body']);
    }

    public function test_v2_request_with_xml_string()
    {
        // XML array will attach signature
        $client = Client::mock();
        $client->shouldReceive('createSignature')->never();
        $client->shouldReceive('attachLegacySignature')->with([
            'foo' => 'bar',
        ])->andReturn(['foo' => 'bar', 'sign' => 'mock-signature'])->once();

        $client->post('certificates', ['xml' => ['foo' => 'bar']]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://api.mch.weixin.qq.com/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: text/xml', $client->getRequestOptions()['headers'][1]);
        $this->assertSame('<xml><foo>bar</foo><sign>mock-signature</sign></xml>', $client->getRequestOptions()['body']);

        // XML string will not attach signature
        $client = Client::mock();
        $client->shouldReceive('createSignature')->never();
        $client->shouldReceive('attachLegacySignature')->never();

        $client->post('certificates', ['xml' => Xml::build(['foo' => 'bar'])]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://api.mch.weixin.qq.com/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: text/xml', $client->getRequestOptions()['headers'][1]);
        $this->assertSame(Xml::build(['foo' => 'bar']), $client->getRequestOptions()['body']);
    }

    public function test_v2_request_with_xml_string_as_body()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->never();
        $client->shouldReceive('attachLegacySignature')->with([
            'foo' => 'bar',
        ])->andReturn(['foo' => 'bar', 'sign' => 'mock-signature']);

        $client->post('certificates', ['body' => Xml::build(['foo' => 'bar'])]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://api.mch.weixin.qq.com/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: text/xml', $client->getRequestOptions()['headers'][1]);
        $this->assertSame(Xml::build(['foo' => 'bar']), $client->getRequestOptions()['body']);
    }

    public function test_v3_upload_media()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->with(
            'POST',
            '/v3/merchant/media/upload',
            \Mockery::on(function ($options) {
                return $options['body'] === json_encode([
                    'filename' => 'image.jpg',
                    'sha256' => hash('sha256', file_get_contents('./tests/fixtures/files/image.jpg')),
                ]);
            })
        )->andReturn('mock-signature');

        $response = new MockResponse('{"media_id":"mock-media-id"}');

        $client->shouldReceive('request')->with(
            'POST',
            '/v3/merchant/media/upload',
            \Mockery::on(function ($options) {
                return $options['body'] !== json_encode([
                    'filename' => 'image.jpg',
                    'sha256' => hash('sha256', file_get_contents('./tests/fixtures/files/image.jpg')),
                ]);
            })
        )->andReturn($response);

        $this->assertSame($response, $client->uploadMedia('/v3/merchant/media/upload', './tests/fixtures/files/image.jpg'));
    }
}
