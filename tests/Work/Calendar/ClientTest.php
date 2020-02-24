<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Calendar;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Calendar\Client;

class ClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $calendar = [
            'organizer' => 'userid1',
            'summary' => 'test_summary',
            'color' => '#FF3030',
            'description' => 'test_describe',
            'shares' => [
                [
                    'userid' => 'userid2',
                ],
                [
                    'userid' => 'userid3',
                ],
            ],
        ];

        $client->expects()
            ->httpPostJson('cgi-bin/oa/calendar/add', compact('calendar'))
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add($calendar));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'summary' => 'test_summary',
            'color' => '#FF3030',
            'description' => 'test_describe',
            'shares' => [
                [
                    'userid' => 'userid2',
                ],
                [
                    'userid' => 'userid3',
                ],
            ],
        ];

        $calendar = $data + ['cal_id' => 'mock-id'];

        $client->expects()
            ->httpPostJson('cgi-bin/oa/calendar/update', compact('calendar'))
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update('mock-id', $data));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('cgi-bin/oa/calendar/get', ['cal_id_list' => ['mock-id1']])
            ->times(2)
            ->andReturn('mock-result');

        // string
        $this->assertSame('mock-result', $client->get('mock-id1'));
        // array
        $this->assertSame('mock-result', $client->get(['mock-id1']));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('cgi-bin/oa/calendar/del', ['cal_id' => 'mock-id'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('mock-id'));
    }
}
