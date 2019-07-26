<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Plugin;

use EasyWeChat\MiniProgram\Plugin\DevClient;
use EasyWeChat\Tests\TestCase;

class DevClientTest extends TestCase
{
    public function testGetUsers()
    {
        $client = $this->mockApiClient(DevClient::class);

        $client->expects()->httpPostJson('wxa/devplugin', [
            'action' => 'dev_apply_list',
            'page' => 1,
            'num' => 10,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUsers());
    }

    public function testAgree()
    {
        $client = $this->mockApiClient(DevClient::class);

        $client->expects()->httpPostJson('wxa/devplugin', [
            'action' => 'dev_agree',
            'appid' => 'mock-appid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->agree('mock-appid'));
    }

    public function testRefuse()
    {
        $client = $this->mockApiClient(DevClient::class);

        $client->expects()->httpPostJson('wxa/devplugin', [
            'action' => 'dev_refuse',
            'reason' => 'mock-reason',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->refuse('mock-reason'));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(DevClient::class);

        $client->expects()->httpPostJson('wxa/devplugin', [
            'action' => 'dev_delete',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete());
    }
}
