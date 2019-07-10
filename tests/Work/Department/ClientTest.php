<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Department;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Department\Client;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/department/create', [
            'foo' => 'bar',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create(['foo' => 'bar']));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/department/update', [
            'id' => 3,
            'foo' => 'bar',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->update(3, ['foo' => 'bar']));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/department/delete', [
            'id' => 3,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete(3));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/department/list', [
            'id' => null,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list());

        $client->expects()->httpGet('cgi-bin/department/list', [
            'id' => 3,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list(3));
    }
}
