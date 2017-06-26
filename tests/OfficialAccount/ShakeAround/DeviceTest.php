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
 * ShakeAroundDeviceTest.php.
 *
 * @author    allen05ren <allen05ren@outlook.com>
 * @copyright 2016 overtrue <i@overtrue.me>
 *
 * @see       https://github.com/overtrue
 * @see       http://overtrue.me
 */

namespace EasyWeChat\Tests\OfficialAccount\ShakeAround;

use EasyWeChat\Applications\OfficialAccount\ShakeAround\DeviceClient;
use EasyWeChat\Tests\TestCase;

class DeviceTest extends TestCase
{
    public function getDevice()
    {
        $device = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\ShakeAround\Device[parseJSON]', [\Mockery::mock('EasyWeChat\Applications\OfficialAccount\Core\AccessToken')]);
        $device->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => $params[1],
            ];
        });

        return $device;
    }

    /**
     * Test apply().
     */
    public function testApply()
    {
        $device = $this->getDevice();

        $expected = [
            'quantity' => 3,
            'apply_reason' => 'test',
        ];

        $result = $device->apply(3, 'test');

        $this->assertStringStartsWith(DeviceClient::API_DEVICE_APPLYID, $result['api']);
        $this->assertSame($expected, $result['params']);

        $expected = [
            'quantity' => 3,
            'apply_reason' => 'test',
            'comment' => 'allen05ren',
        ];

        $result = $device->apply(3, 'test', 'allen05ren');

        $this->assertSame($expected, $result['params']);

        $expected = [
            'quantity' => 3,
            'apply_reason' => 'test',
            'comment' => 'allen05ren',
            'poi_id' => 1234,
        ];

        $result = $device->apply(3, 'test', 'allen05ren', 1234);

        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test getStatus().
     */
    public function testGetStatus()
    {
        $device = $this->getDevice();

        $expected = [
            'apply_id' => 12345678,
        ];

        $result = $device->getStatus(12345678);

        $this->assertStringStartsWith(DeviceClient::API_DEVICE_APPLYSTATUS, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $device = $this->getDevice();

        $expected = [
            'device_identifier' => [
                'device_id' => 10100,
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                'major' => 10001,
                'minor' => 10002,
            ],
            'comment' => 'allen05ren',
        ];

        $result = $device->update([
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ], 'allen05ren');

        $this->assertStringStartsWith(DeviceClient::API_DEVICE_UPDATE, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test bindLocation().
     *
     * @expectedException \EasyWeChat\Exceptions\InvalidArgumentException
     */
    public function testBindLocation()
    {
        $device = $this->getDevice();

        $expected = [
            'device_identifier' => [
                'device_id' => 10100,
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                'major' => 10001,
                'minor' => 10002,
            ],
            'poi_id' => 1234,
        ];

        $result = $device->bindLocation([
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ], 1234);

        $this->assertStringStartsWith(DeviceClient::API_DEVICE_BINDLOCATION, $result['api']);
        $this->assertSame($expected, $result['params']);

        $expected = [
            'device_identifier' => [
                'device_id' => 10100,
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                'major' => 10001,
                'minor' => 10002,
            ],
            'poi_id' => 1234,
            'type' => 2,
            'poi_appid' => 'wxappid',
        ];

        $result = $device->bindLocation([
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ], 1234, 2, 'wxappid');

        $this->assertSame($expected, $result['params']);

        $result = $device->bindLocation([
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ], 1234, 2);
    }

    /**
     * Test fetchByIds().
     */
    public function testFetchByIds()
    {
        $device = $this->getDevice();

        $expected = [
            'type' => 1,
            'device_identifiers' => [
                'device_id' => 10100,
                'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
                'major' => 10001,
                'minor' => 10002,
            ],
        ];

        $result = $device->fetchByIds([
            'device_id' => 10100,
            'uuid' => 'FDA50693-A4E2-4FB1-AFCF-C6EB07647825',
            'major' => 10001,
            'minor' => 10002,
        ]);

        $this->assertStringStartsWith(DeviceClient::API_DEVICE_SEARCH, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test pagination().
     */
    public function testPagination()
    {
        $device = $this->getDevice();

        $expected = [
            'type' => 2,
            'last_seen' => 0,
            'count' => 10,
        ];

        $result = $device->pagination(0, 10);

        $this->assertStringStartsWith(DeviceClient::API_DEVICE_SEARCH, $result['api']);
        $this->assertSame($expected, $result['params']);
    }

    /**
     * Test fetchByApplyId().
     */
    public function testFetchByApplyId()
    {
        $device = $this->getDevice();

        $expected = [
            'type' => 3,
            'apply_id' => 12345678,
            'last_seen' => 0,
            'count' => 10,
        ];

        $result = $device->fetchByApplyId(12345678, 0, 10);

        $this->assertStringStartsWith(DeviceClient::API_DEVICE_SEARCH, $result['api']);
        $this->assertSame($expected, $result['params']);
    }
}
