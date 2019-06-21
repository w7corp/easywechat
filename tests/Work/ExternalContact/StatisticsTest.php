<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\ExternalContact;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\ExternalContact\StatisticsClient;

class StatisticsTest extends TestCase
{
    public function testUserBehavior()
    {
        $client = $this->mockApiClient(StatisticsClient::class);

        $params = [
            'userid' => [
                'zhangsan',
                'lisi',
            ],
            'start_time' => 1536508800,
            'end_time' => 1536940800,
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_user_behavior_data', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userBehavior(['zhangsan', 'lisi'], 1536508800, 1536940800));
    }
}
