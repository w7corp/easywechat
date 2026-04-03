<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel;

use EasyWeChat\Kernel\ServerResponse;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class ServerResponseTest extends TestCase
{
    public function test_to_string()
    {
        $response = ServerResponse::make(new Response(200, ['X-Foo' => 'bar'], 'foo'));

        $responseLines = explode("\r\n", (string) $response);
        $this->assertEquals('HTTP/1.1 200 OK', $responseLines[0]);
        $this->assertEquals('X-Foo: bar', $responseLines[1]);
        $this->assertEquals('foo', $responseLines[3]);
    }

    public function test_to_string_without_headers()
    {
        $response = ServerResponse::make(new Response(200, [], 'foo'));

        $responseLines = explode("\r\n", (string) $response);
        $this->assertEquals('HTTP/1.1 200 OK', $responseLines[0]);
        $this->assertEquals('foo', $responseLines[2]);
    }

    public function test_it_can_send_response()
    {
        \ob_start();
        $response = ServerResponse::make(new Response(200, ['X-Foo' => 'bar'], 'foo'));
        $response->sendContent();
        $contents = \ob_get_contents();
        \ob_end_clean();

        $this->assertSame('foo', $contents);
    }

    public function test_withers_keep_server_response_wrapper()
    {
        $response = ServerResponse::make(new Response(200, ['X-Foo' => 'bar'], 'foo'));

        $updated = $response
            ->withProtocolVersion('2.0')
            ->withHeader('X-Bar', 'baz')
            ->withAddedHeader('X-Bar', 'qux')
            ->withBody(Stream::create('bar'));

        $this->assertInstanceOf(ServerResponse::class, $updated);
        $this->assertSame('2.0', $updated->getProtocolVersion());
        $this->assertSame(['baz', 'qux'], $updated->getHeader('X-Bar'));
        $this->assertSame('bar', (string) $updated->getBody());

        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertFalse($response->hasHeader('X-Bar'));
        $this->assertSame('foo', (string) $response->getBody());
    }

    public function test_with_status_and_without_header_return_new_wrapped_response()
    {
        $response = ServerResponse::make(new Response(200, ['X-Foo' => 'bar'], 'foo'));

        $updated = $response
            ->withStatus(202, 'Accepted')
            ->withoutHeader('X-Foo');

        $this->assertInstanceOf(ServerResponse::class, $updated);
        $this->assertSame(202, $updated->getStatusCode());
        $this->assertSame('Accepted', $updated->getReasonPhrase());
        $this->assertFalse($updated->hasHeader('X-Foo'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('X-Foo'));
    }
}
