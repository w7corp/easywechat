<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\RealtimeLog;

use EasyWeChat\MiniProgram\RealtimeLog\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSearch()
    {
        $client = $this->mockApiClient(Client::class)->makePartial();

        $client->expects()->httpGet('wxaapi/userlog/userlog_search', [
                'date' => '20191217',
                'begintime' => 1576549163,
                'endtime' => 1576559963,
                'level' => 2,
            ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->search('20191217', 1576549163, 1576559963, ['level' => 2]));
    }
}
