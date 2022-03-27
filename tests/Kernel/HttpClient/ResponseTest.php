<?php

namespace EasyWeChat\Tests\Kernel\HttpClient;

use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Tests\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ResponseTest extends TestCase
{
    public function test_it_will_throw_if_body_is_empty()
    {
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getContent')->andReturns('');
        });

        $this->expectException(BadResponseException::class);
        (new Response($response))->toArray();
    }

    public function test_it_can_decode_xml()
    {
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getHeaders')->andReturns([], ['content-type' => ['text/xml']], ['content-type' => ['text/xml']]);
            $mock->shouldReceive('getContent')->andReturns('<xml><foo>bar</foo></xml>', '<xml><foo>bar</foo></xml>', '<invalid xml>');
        });

        $this->assertSame(['foo' => 'bar'], (new Response($response))->toArray());
        $this->assertSame(['foo' => 'bar'], (new Response($response))->toArray());

        $this->expectException(BadResponseException::class);
        (new Response($response))->toArray();
    }

    public function test_it_support_array_access()
    {
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getHeaders')->andReturns([]);
            $mock->shouldReceive('getContent')->andReturns('{"foo":"bar"}');
            $mock->shouldReceive('toArray')->andReturns(['foo' => 'bar']);
        });

        $response = (new Response($response));

        $this->assertSame('bar', $response['foo']);
        $this->assertNull($response['not-exist']);

        $this->assertTrue(isset($response['foo']));
        $this->assertFalse(isset($response['not-exist']));
    }

    public function test_it_support_to_json()
    {
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getHeaders')->andReturns([]);
            $mock->shouldReceive('getContent')->andReturns('{"foo":"bar"}');
            $mock->shouldReceive('toArray')->andReturns(['foo' => 'bar']);
        });

        $response = (new Response($response));

        $this->assertSame('{"foo":"bar"}', $response->toJson());
    }

    public function test_it_can_get_headers()
    {
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getHeaders')->andReturns([
                'content-type' => ['text/xml; encoding=utf-8'],
                'cache-control' => ['max-age=3600', 'public'],
            ]);
        });

        $response = (new Response($response));

        $this->assertTrue($response->hasHeader('content-type'));
        $this->assertSame(['text/xml; encoding=utf-8'], $response->getHeader('content-type'));
        $this->assertSame('max-age=3600,public', $response->getHeaderLine('cache-control'));
    }

    public function test_it_can_save_content_to_files()
    {
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getHeaders')->andReturns([]);
            $mock->shouldReceive('getContent')->andReturns('{"foo":"bar"}');
            $mock->shouldReceive('toArray')->andReturns(['foo' => 'bar']);
        });

        $response = (new Response($response));
        $tmpFile = \sys_get_temp_dir().'/'.\uniqid('', true);
        $response->saveAs($tmpFile);

        $this->assertSame('{"foo":"bar"}', \file_get_contents($tmpFile));
        @\unlink($tmpFile);

        // throw when response get content failed
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getContent')->with(true)->andThrow(new \Exception('mock-exception'))->once();
            $mock->shouldReceive('getContent')->with(false)->andReturns('{"errcode":40029, "errmsg":"invalid code"}')->once();
        });
        $response = (new Response($response));

        $this->expectException(BadResponseException::class);
        $this->expectExceptionMessageMatches('/Cannot save response to .*?: {"errcode":40029, "errmsg":"invalid code"}/');

        $response->saveAs($tmpFile);
    }
}
