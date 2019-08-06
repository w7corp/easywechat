<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\ShakeAround;

use EasyWeChat\OfficialAccount\ShakeAround\PageClient;
use EasyWeChat\Tests\TestCase;

class PageClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(PageClient::class);

        $client->expects()->httpPostJson('shakearound/page/add', ['foo' => 'bar'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create(['foo' => 'bar']));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(PageClient::class);

        $client->expects()->httpPostJson('shakearound/page/update', [
            'page_id' => 3,
            'foo' => 'bar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(3, ['foo' => 'bar']));
    }

    public function testListByIds()
    {
        $client = $this->mockApiClient(PageClient::class);

        $client->expects()->httpPostJson('shakearound/page/search', [
            'type' => 1,
            'page_ids' => [1, 2, 5],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listByIds([1, 2, 5]));
    }

    public function testList()
    {
        $client = $this->mockApiClient(PageClient::class);

        $client->expects()->httpPostJson('shakearound/page/search', [
            'type' => 2,
            'begin' => 3,
            'count' => 20,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(3, 20));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(PageClient::class);

        $client->expects()->httpPostJson('shakearound/page/delete', [
            'page_id' => 3,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete(3));
    }
}
