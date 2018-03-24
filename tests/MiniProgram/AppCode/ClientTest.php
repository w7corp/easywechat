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

use EasyWeChat\Kernel\Http\StreamResponse;
use EasyWeChat\MiniProgram\AppCode\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    protected $mockStream;

    public function setUp()
    {
        parent::setUp();

        $this->mockStream = new \EasyWeChat\Kernel\Http\Response();
    }

    public function testGetAppCode()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('wxa/getwxacode', 'POST', ['json' => [
            'path' => 'foo-path',
            'width' => 430,
        ]])->andReturn($this->mockStream)->once();

        $this->assertInstanceOf(StreamResponse::class, $client->get('foo-path', [
            'width' => 430,
        ]));
    }

    public function testGetAppCodeUnlimit()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('wxa/getwxacodeunlimit', 'POST', ['json' => [
            'scene' => 'scene',
            'page' => '/app/pages/hello',
        ]])->andReturn($this->mockStream)->once();

        $this->assertInstanceOf(StreamResponse::class, $client->getUnlimit('scene', [
            'page' => '/app/pages/hello',
        ]));
    }

    public function testCreateQrCode()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('cgi-bin/wxaapp/createwxaqrcode', 'POST', ['json' => [
            'path' => 'foo-path',
            'width' => null,
        ]])->andReturn($this->mockStream)->once();

        $this->assertInstanceOf(StreamResponse::class, $client->getQrCode('foo-path'));
    }
}
