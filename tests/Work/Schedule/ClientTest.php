<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Schedule;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Schedule\Client;

class ClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);

        $schedule = [
            'organizer' => 'userid1',
            'summary' => 'test_summary',
            'description' => 'test_describe',
            'attendees' => [
                [
                    'userid' => 'userid2',
                ],
            ],
            'start_time' => 1571274600,
            'end_time' => 1571320210,
        ];

        $client->expects()
            ->httpPostJson('cgi-bin/oa/schedule/add', compact('schedule'))
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add($schedule));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'organizer' => 'userid1',
            'summary' => 'test_summary',
            'description' => 'test_describe',
            'attendees' => [
                [
                    'userid' => 'userid2',
                ],
            ],
            'start_time' => 1571274600,
            'end_time' => 1571320210,
        ];

        $schedule = $data + ['schedule_id' => 'mock-id'];

        $client->expects()
            ->httpPostJson('cgi-bin/oa/schedule/update', compact('schedule'))
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update('mock-id', $data));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('cgi-bin/oa/schedule/get', ['schedule_id_list' => ['mock-id1']])
            ->times(2)
            ->andReturn('mock-result');

        // string
        $this->assertSame('mock-result', $client->get('mock-id1'));
        // array
        $this->assertSame('mock-result', $client->get(['mock-id1']));
    }

    public function testGetByCalendar()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('cgi-bin/oa/schedule/get_by_calendar', [
                'offset' => 0,
                'limit' => 500,
                'cal_id' => 'mock-id',
            ])
            ->times(2)
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getByCalendar('mock-id'));
        $this->assertSame('mock-result', $client->getByCalendar('mock-id', 0, 500));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('cgi-bin/oa/schedule/del', ['schedule_id' => 'mock-id'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('mock-id'));
    }
}
