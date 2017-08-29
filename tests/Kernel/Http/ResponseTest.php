<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Kernel\Http;

use EasyWeChat\Kernel\Http\Response;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testBasicFeatures()
    {
        $response = new Response(200, ['content-type:application/json'], '{"name": "easywechat"}');

        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $response);

        $this->assertSame('{"name": "easywechat"}', (string) $response);
        $this->assertSame('{"name": "easywechat"}', $response->getBodyContents());
        $this->assertSame('{"name":"easywechat"}', $response->toJson());
        $this->assertSame(['name' => 'easywechat'], $response->toArray());
        $this->assertSame('easywechat', $response->toObject()->name);
        $this->assertInstanceOf(Collection::class, $response->toCollection());
        $this->assertSame(['name' => 'easywechat'], $response->toCollection()->all());
    }

    public function testXMLContents()
    {
        $response = new Response(200, ['Content-Type' => ['application/xml']], '<xml><foo>foo</foo><bar>bar</bar></xml>');
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $response->toArray());

        $response = new Response(200, ['Content-Type' => ['text/xml']], '<xml><foo>foo</foo><bar>bar</bar></xml>');
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $response->toArray());

        $response = new Response(200, ['Content-Type' => ['text/html']], '<xml><foo>foo</foo><bar>bar</bar></xml>');
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $response->toArray());
    }

    public function testInvalidArrayableContents()
    {
        $response = new Response(200, [], 'not json string');

        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $response);

        $this->assertSame([], $response->toArray());
    }
}
