<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ShakeAroundGroupTest.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\Tests\OfficialAccount\ShakeAround;

use EasyWeChat\Applications\OfficialAccount\ShakeAround\GroupClient;
use EasyWeChat\Tests\TestCase;

class GroupTest extends TestCase
{
    public function getGroup()
    {
        $group = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\ShakeAround\Group[parseJSON]', [\Mockery::mock('EasyWeChat\Applications\OfficialAccount\Core\AccessToken')]);
        $group->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        return $group;
    }

    /**
     * Test add().
     */
    public function testAdd()
    {
        $group = $this->getGroup();

        $expected = [
            'group_name' => 'overtrue',
        ];

        $result = $group->add('overtrue');

        $this->assertStringStartsWith(GroupClient::API_ADD, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $group = $this->getGroup();

        $expected = [
            'group_id' => 12345678,
            'group_name' => 'allen05ren',
        ];

        $result = $group->update(12345678, 'allen05ren');

        $this->assertStringStartsWith(GroupClient::API_UPDATE, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $group = $this->getGroup();

        $expected = [
            'group_id' => 12345678,
        ];

        $result = $group->delete(12345678);

        $this->assertStringStartsWith(GroupClient::API_DELETE, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $group = $this->getGroup();

        $expected = [
            'begin' => 0,
            'count' => 10,
        ];

        $result = $group->lists(0, 10);

        $this->assertStringStartsWith(GroupClient::API_GET_LIST, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test getDetails().
     */
    public function testGetDetails()
    {
        $group = $this->getGroup();

        $expected = [
            'group_id' => 12345678,
            'begin' => 0,
            'count' => 10,
        ];

        $result = $group->getDetails(12345678, 0, 10);

        $this->assertStringStartsWith(GroupClient::API_GET_DETAIL, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test addDevice().
     */
    public function testAddDevice()
    {
        $group = $this->getGroup();

        $expected = [
            'group_id' => 12345678,
            'device_identifiers' => [
                'device_id' => 10100,
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                'major' => 10001,
                'minor' => 10002,
            ],
        ];

        $result = $group->addDevice(12345678, [
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ]);

        $this->assertStringStartsWith(GroupClient::API_ADD_DEVICE, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test removeDevice().
     */
    public function testRemoveDevice()
    {
        $group = $this->getGroup();

        $expected = [
            'group_id' => 12345678,
            'device_identifiers' => [
                'device_id' => 10100,
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                'major' => 10001,
                'minor' => 10002,
            ],
        ];

        $result = $group->removeDevice(12345678, [
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ]);

        $this->assertStringStartsWith(GroupClient::API_DELETE_DEVICE, $result['api']);
        $this->assertSame($expected, $result['params']);
    }
}
