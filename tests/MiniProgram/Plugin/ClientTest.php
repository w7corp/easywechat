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

use EasyWeChat\MiniProgram\Plugin\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testApply()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/plugin', ['action' => 'apply', 'plugin_appid' => 'plugin-app-id'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->apply('plugin-app-id'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/plugin', ['action' => 'list'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->list());
    }

    public function testUnbind()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('wxa/plugin', ['action' => 'unbind', 'plugin_appid' => 'plugin-app-id'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->unbind('plugin-app-id'));
    }
}
