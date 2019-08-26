<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Goods;

use EasyWeChat\OfficialAccount\Goods\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testAdd()
    {
        $params = ['foo' => 'bar'];

        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('scan/product/v2/add', ['product' => $params])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add($params));
    }

    public function testUpdate()
    {
        $params = ['foo' => 'bar'];

        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('scan/product/v2/add', ['product' => $params])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update($params));
    }

    public function testStatus()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('scan/product/v2/status', [
            'status_ticket' => 'mock-ticket',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->status('mock-ticket'));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('scan/product/v2/getinfo', [
            'product' => [
                'pid' => 'mock-pid',
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('mock-pid'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('scan/product/v2/getinfobypage', [
            'page_context' => 'mock-context',
            'page_num' => 1,
            'page_size' => 10,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list('mock-context'));
    }
}
