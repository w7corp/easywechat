<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Fundamental;

use EasyWeChat\OfficialAccount\Fundamental\Client;
use EasyWeChat\Tests\TestCase;

class FundamentalAPITest extends TestCase
{
    /**
     * Test clearQuota().
     */
    public function testClearQuota()
    {
        $result = $this->make()->clearQuota();

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/clear_quota', $result['api']);
        $this->assertSame(['appid' => 'i-am-app-id'], $result['params']);
    }

    /**
     * Test getCallbackIp().
     */
    public function testGetCallbackIp()
    {
        $result = $this->make()->getCallbackIp();

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/getcallbackip', $result['api']);
    }

    private function make()
    {
        $accessToken = \Mockery::mock('EasyWeChat\OfficialAccount\Core\AccessToken', function ($mock) {
            $mock->shouldReceive('getAppId')->andReturn('i-am-app-id');
        });
        $api = \Mockery::mock('EasyWeChat\OfficialAccount\Fundamental\Client[parseJSON]', [$accessToken]);
        $api->shouldReceive('parseJSON')->andReturnUsing(function ($api, $params) {
            if (isset($params[1])) {
                return ['api' => $params[0], 'params' => $params[1]];
            }

            return ['api' => $params[0]];
        });

        return $api;
    }
}
