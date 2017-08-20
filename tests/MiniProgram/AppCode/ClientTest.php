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

        $this->mockStream = \Mockery::mock(\EasyWeChat\Kernel\Http\Response::class);
    }

    public function testGetAppCode()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('wxa/getwxacode', 'POST', ['json' => [
            'path' => 'foo-path',
            'width' => null,
            'auto_color' => null,
            'line_color' => null,
        ]])->andReturn($this->mockStream)->once();

        $this->assertInstanceOf(\EasyWeChat\Kernel\Http\Response::class, $client->get('foo-path'));
    }

    public function testGetAppCodeUnlimit()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('wxa/getwxacodeunlimit', 'POST', ['json' => [
            'scene' => 'scene',
            'page' => null,
            'width' => null,
            'auto_color' => null,
            'line_color' => null,
        ]])->andReturn($this->mockStream)->once();

        $this->assertInstanceOf(\EasyWeChat\Kernel\Http\Response::class, $client->getUnlimit('scene'));
    }

    public function testCreateQrCode()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->requestRaw('cgi-bin/wxaapp/createwxaqrcode', 'POST', ['json' => [
            'path' => 'foo-path',
            'width' => null,
        ]])->andReturn($this->mockStream)->once();

        $this->assertInstanceOf(\EasyWeChat\Kernel\Http\Response::class, $client->qrcode('foo-path'));
    }
}
