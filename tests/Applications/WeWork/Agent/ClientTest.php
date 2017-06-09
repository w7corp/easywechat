<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Applications\WeWork\Agent;

use EasyWeChat\Tests\TestCase;
use Mockery as m;

class ClientTest extends TestCase
{
    /**
     * Test get().
     */
    public function testGet()
    {
        $result = $this->getClient()->get(1);

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/agent/get', $result[1][0]);
        $this->assertSame(['agentid' => 1], $result[1][1]);
    }

    /**
     * Test set().
     */
    public function testSet()
    {
        $result = $this->getClient()->set($expected = [
            'agentid' => 5,
            'report_location_flag' => 0,
            'logo_mediaid' => 'xxxxx',
            'name' => 'NAME',
            'description' => 'DESC',
            'redirect_domain' => 'xxxxxx',
            'isreportenter' => 0,
            'home_url' => 'http://www.qq.com',
        ]);

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/agent/set', $result[1][0]);
        $this->assertSame($expected, $result[1][1]);
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $result = $this->getClient()->lists();

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/agent/list', $result[1][0]);
    }

    /**
     * Get client.
     *
     * @return \EasyWeChat\Applications\WeWork\Agent\Client
     */
    private function getClient()
    {
        return m::mock('EasyWeChat\Applications\WeWork\Agent\Client[parseJSON]', [m::mock('EasyWeChat\Applications\WeWork\Core\AccessToken')], function ($mock) {
            $mock->shouldReceive('parseJSON')->andReturnUsing(function (...$args) {
                return $args;
            });
        });
    }
}
