<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Live;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Live\Client;

class ClientTest extends TestCase
{
    public function testGetUserLivingId()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/living/get_user_livingid', [
            'userid' => 'mock-userid',
            'begin_time' => 1586136317,
            'end_time' => 1586236317,
            'next_key' => '0',
            'limit' => 100
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUserLivingId('mock-userid', 1586136317, 1586236317));
    }

    public function testGetLiving()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/living/get_living_info', [
            'livingid' => 'mock-livingid'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getLiving('mock-livingid'));
    }

    public function testGetWatchStat()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/living/get_watch_stat', [
            'livingid' => 'mock-livingid',
            'next_key' => '0'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getWatchStat('mock-livingid'));
    }
}
