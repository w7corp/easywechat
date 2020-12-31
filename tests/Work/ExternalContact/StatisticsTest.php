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
            'partyid' => [],
            'start_time' => 1536508800,
            'end_time' => 1536940800,
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_user_behavior_data', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userBehavior(['zhangsan', 'lisi'], 1536508800, 1536940800,[]));
    }

    public function testGroupChatStatistic(): void
    {
        $client = $this->mockApiClient(StatisticsClient::class);

        $params = [
            'day_begin_time' => 1600272000,
            'day_end_time' => 1600444800,
            'owner_filter' => [
                'userid_list' => ['zhangsan']
            ],
            'order_by' => 2,
            'order_asc' => 0,
            'offset' => 0,
            'limit' => 1000
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/statistic', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->groupChatStatistic($params));
    }


    public function testGroupChatStatisticGroupByDay(): void
    {
        $client = $this->mockApiClient(StatisticsClient::class);

        $params = [
            'day_begin_time' => 1600272000,
            'day_end_time' => 1600444800,
            'owner_filter' => [
                'userid_list' => ['userid1', 'userid2']
            ]
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/groupchat/statistic_group_by_day', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->groupChatStatisticGroupByDay(1600272000, 1600444800, ['userid1', 'userid2']));
    }
}
