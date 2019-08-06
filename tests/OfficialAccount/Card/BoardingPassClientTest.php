<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Card;

use EasyWeChat\OfficialAccount\Card\BoardingPassClient;
use EasyWeChat\Tests\TestCase;

class BoardingPassClientTest extends TestCase
{
    public function testCheckin()
    {
        $client = $this->mockApiClient(BoardingPassClient::class);

        $params = [
            'foo' => 'bar',
        ];
        $client->expects()->httpPostJson('card/boardingpass/checkin', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->checkin($params));
    }
}
