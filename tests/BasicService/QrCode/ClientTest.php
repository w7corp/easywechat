<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\BasicService\QrCode;

use EasyWeChat\BasicService\QrCode\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testForever()
    {
        $client = $this->mockApiClient(Client::class, 'create');

        // int
        $client->expects()->create(Client::SCENE_QR_FOREVER, [
            'scene_id' => 99999,
        ], false)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->forever(99999));

        // string
        $client->expects()->create(Client::SCENE_QR_FOREVER_STR, [
            'scene_str' => 'foo',
        ], false)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->forever('foo'));
    }

    public function testTemporary()
    {
        $client = $this->mockApiClient(Client::class, 'create');

        // int
        $client->expects()->create(Client::SCENE_QR_TEMPORARY, [
            'scene_id' => 99999,
        ], true, null)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->temporary(99999));

        // string
        $client->expects()->create(Client::SCENE_QR_TEMPORARY_STR, [
            'scene_str' => 'foo',
        ], true, 7200)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->temporary('foo', 7200));
    }

    public function testUrl()
    {
        $client = $this->mockApiClient(Client::class, 'create');
        $this->assertSame(sprintf('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=%s', 'ticket%3F%2F'), $client->url('ticket?/'));
    }

    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class, 'create')->shouldAllowMockingProtectedMethods();
        $client->makePartial();

        // temporary = true, expireSeconds = null
        $client->expects()->httpPostJson('qrcode/create', [
            'action_name' => Client::SCENE_QR_CARD,
            'action_info' => ['scene' => ['foo' => 'bar']],
            'expire_seconds' => 7 * Client::DAY,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create(Client::SCENE_QR_CARD, ['foo' => 'bar']));

        // temporary = false, expireSeconds = null
        $client->expects()->httpPostJson('qrcode/create', [
            'action_name' => Client::SCENE_QR_FOREVER,
            'action_info' => ['scene' => ['foo' => 'bar']],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create(Client::SCENE_QR_FOREVER, ['foo' => 'bar'], false));

        // temporary = false, expireSeconds = 500
        $client->expects()->httpPostJson('qrcode/create', [
            'action_name' => Client::SCENE_QR_TEMPORARY_STR,
            'action_info' => ['scene' => ['foo' => 'bar']],
            'expire_seconds' => 500,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create(Client::SCENE_QR_TEMPORARY_STR, ['foo' => 'bar'], true, 500));
    }
}
