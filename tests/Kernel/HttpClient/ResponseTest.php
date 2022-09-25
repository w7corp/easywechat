<?php

namespace EasyWeChat\Tests\Kernel\HttpClient;

use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
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

        $response = (new Response($response))->toArray();
        $this->assertIsArray($response);
        $this->assertEmpty($response);
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

    public function test_it_can_transform_to_data_url()
    {
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getHeaders')->andReturns(['content-type' => ['application/json;encoding=utf-8']]);
            $mock->shouldReceive('getContent')->andReturns('{"foo":"bar"}');
            $mock->shouldReceive('toArray')->andReturns(['foo' => 'bar']);
        });

        $response = (new Response($response));

        $this->assertSame('data:application/json;encoding=utf-8;base64,eyJmb28iOiJiYXIifQ==', $response->toDataUrl());
    }

    public function test_it_can_judge_failure_with_custom_callback()
    {
        // from http code 200
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getStatusCode')->andReturns(200)->times(2);
            $mock->shouldReceive('getHeaders')->andReturns(['content-type' => ['application/json;encoding=utf-8']]);
        });

        $response = (new Response($response));

        $this->assertFalse($response->isFailed());  // 200
        $this->assertTrue($response->isSuccessful());

        // from http code 400
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getHeaders')->andReturns(['content-type' => ['application/json;encoding=utf-8']]);
            $mock->shouldReceive('getStatusCode')->andReturns(400)->times(2);
        });

        $response = (new Response($response));
        $this->assertTrue($response->isFailed());   // 400
        $this->assertFalse($response->isSuccessful());

        // custom callback
        $response = \Mockery::mock(ResponseInterface::class, function ($mock) {
            $mock->shouldReceive('getStatusCode')->never();
            $mock->shouldReceive('getHeaders')->andReturns(['content-type' => ['application/json;encoding=utf-8']]);
            $mock->shouldReceive('getContent')->andReturns(\json_encode(['errcode' => 40029, 'errmsg' => 'invalid code']));
            $mock->shouldReceive('toArray')->andReturns(['errcode' => 40029, 'errmsg' => 'invalid code']);
        });

        $response = (new Response($response));

        $response->judgeFailureUsing(function ($response) {
            return ! empty($response->toArray()['errcode'] ?? null);
        });

        $this->assertTrue($response->isFailed());
        $this->assertFalse($response->isSuccessful());
    }

    public function test_it_can_has_global_throw_settings()
    {
        $httpClient = new MockHttpClient(new MockResponse('{"foo":"bar"}', ['http_code' => 403]));
        $response = (new Response($httpClient->request('GET', '/foo'), throw: false));

        // global throw setting is false
        try {
            $this->assertSame(['foo' => 'bar'], $response->toArray());
            $this->assertSame('{"foo":"bar"}', $response->getContent());
        } catch (\Exception $e) {
            $this->fail('should not throw exception');
        }

        // global throw setting is ignored
        try {
            $response->toArray(true);
            $this->fail('should throw exception');
        } catch (\Exception $e) {
            $this->assertSame('HTTP 403 returned for "https://example.com/foo".', $e->getMessage());
        }

        try {
            $response->getContent(true);
            $this->fail('should throw exception');
        } catch (\Exception $e) {
            $this->assertSame('HTTP 403 returned for "https://example.com/foo".', $e->getMessage());
        }
    }
}
