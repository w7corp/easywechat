<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Menu;

use EasyWeChat\OfficialAccount\Menu\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testList()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/menu/get')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list());
    }

    public function testCurrent()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/get_current_selfmenu_info')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->current());
    }

    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        // with match rule
        $client->expects()->httpPostJson('cgi-bin/menu/addconditional', [
            'button' => ['foo' => 'bar'],
            'matchrule' => ['tag_id' => 1],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create(['foo' => 'bar'], ['tag_id' => 1]));

        // without match rule
        $client->expects()->httpPostJson('cgi-bin/menu/create', [
            'button' => ['foo' => 'bar'],
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create(['foo' => 'bar']));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        // without menu id
        $client->expects()->httpGet('cgi-bin/menu/delete')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete());

        // without match rule
        $client->expects()->httpPostJson('cgi-bin/menu/delconditional', ['menuid' => 20181723])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete(20181723));
    }

    public function testMatch()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/menu/trymatch', ['user_id' => 'mock-user-id'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->match('mock-user-id'));
    }
}
