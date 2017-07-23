<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Applications\WeWork\Attendance;

use EasyWeChat\Tests\TestCase;
use Mockery as m;

class ClientTest extends TestCase
{
    /**
     * Test getCheckinData().
     */
    public function testGetCheckinData()
    {
        $result = $this->getClient()->getCheckinData(12, 34, ['zhangsan', 'lisi']);

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/checkin/getcheckindata', $result[1][0]);
        $this->assertSame([
            'opencheckindatatype' => 3,
            'starttime' => 12,
            'endtime' => 34,
            'useridlist' => ['zhangsan', 'lisi'],
        ], $result[1][1]);

        $result = $this->getClient()->getCheckinData(12, 34, ['zhangsan', 'lisi'], 1);

        $this->assertSame([
            'opencheckindatatype' => 1,
            'starttime' => 12,
            'endtime' => 34,
            'useridlist' => ['zhangsan', 'lisi'],
        ], $result[1][1]);
    }

    /**
     * Get Attendance Client.
     *
     * @return \EasyWeChat\Applications\WeWork\Attendance\Client
     */
    private function getClient()
    {
        return m::mock('EasyWeChat\Applications\WeWork\Attendance\Client[parseJSON]', [m::mock('EasyWeChat\Applications\WeWork\Core\AccessToken')], function ($mock) {
            $mock->shouldReceive('parseJSON')->andReturnUsing(function (...$args) {
                return $args;
            });
        });
    }
}
