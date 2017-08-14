<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\WeWork\Agent;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\WeWork\Agent\Client;

class ClientTest extends TestCase
{
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['agent_id' => 203874]));
        $client->expects()->httpGet('agent/get', ['agentid' => 203874])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->get(203874));
    }

    public function testSet()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['agent_id' => 203874]));
        $client->expects()->httpPostJson('agent/set', [
            'agentid' => 203874,
            'foo' => 'bar',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->set(203874, ['foo' => 'bar']));
    }

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('agent/list')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list());
    }
}
