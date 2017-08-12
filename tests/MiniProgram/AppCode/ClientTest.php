<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\AppCode;

use EasyWeChat\MiniProgram\AppCode\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    protected $mockStream;

    public function setUp()
    {
        parent::setUp();

        $this->mockStream = \Mockery::mock(\Psr\Http\Message\StreamInterface::class, function ($mock) {
            $mock->expects()->getBody()->andReturn('mock-body');
        });
    }

    public function testGetAppCode()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('wxa/getwxacode', 'POST', ['json' => [
            'path' => 'foo-path',
            'width' => 430,
            'auto_color' => false,
            'line_color' => ['r' => 0, 'g' => 0, 'b' => 0],
        ]])->andReturn($this->mockStream)->once();

        $this->assertSame('mock-body', $client->getAppCode('foo-path'));
    }

    public function testGetAppCodeUnlimit()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('wxa/getwxacodeunlimit', 'POST', ['json' => [
            'scene' => 'scene',
            'width' => 430,
            'auto_color' => false,
            'line_color' => ['r' => 0, 'g' => 0, 'b' => 0],
        ]])->andReturn($this->mockStream)->once();

        $this->assertSame('mock-body', $client->getAppCodeUnlimit('scene'));
    }

    public function testCreateQrCode()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('cgi-bin/wxaapp/createwxaqrcode', 'POST', ['json' => [
            'path' => 'foo-path',
            'width' => 430,
        ]])->andReturn($this->mockStream)->once();

        $this->assertSame('mock-body', $client->createQrCode('foo-path'));
    }
}
