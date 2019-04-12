<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\ActivityMessage;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniProgram\ActivityMessage\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCreateActivityId()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/message/wxopen/activityid/create')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createActivityId());
    }

    public function testUpdateMessageWithInvalidState()
    {
        $client = $this->mockApiClient(Client::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"state" should be "0" or "1".');
        $client->updateMessage('mock-activity-id', 666);
    }

    public function testUpdateMessage()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/message/wxopen/updatablemsg/send', [
            'activity_id' => 'mock-activity-id',
            'target_state' => 0,
            'template_info' => [
                'parameter_list' => [
                    [
                        'name' => 'member_count',
                        'value' => 'mock-member-count',
                    ],
                    [
                        'name' => 'room_limit',
                        'value' => 'mock-room-limit',
                    ],
                    [
                        'name' => 'path',
                        'value' => 'mock-path',
                    ],
                    [
                        'name' => 'version_type',
                        'value' => 'develop',
                    ],
                ],
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateMessage('mock-activity-id', 0, [
            'member_count' => 'mock-member-count',
            'room_limit' => 'mock-room-limit',
            'path' => 'mock-path',
            'version_type' => 'develop',
            'foo' => 'bar',
        ]));

        // invalid version type
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value of attribute "version_type".');
        $this->assertSame('mock-result', $client->updateMessage('mock-activity-id', 0, [
            'member_count' => 'mock-member-count',
            'room_limit' => 'mock-room-limit',
            'path' => 'mock-path',
            'version_type' => 'mock-version-type',
            'foo' => 'bar',
        ]));
    }
}
