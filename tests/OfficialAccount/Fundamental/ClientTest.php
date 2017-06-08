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

use EasyWeChat\Applications\OfficialAccount\Core\AccessToken;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * Test clearQuota().
     */
    public function testClearQuota()
    {
        $result = $this->make('wxappid')->clearQuota();

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/clear_quota', $result['api']);
        $this->assertSame(['appid' => 'wxappid'], $result['params']);
    }

    /**
     * Test getCallbackIp().
     */
    public function testGetCallbackIp()
    {
        $result = $this->make()->getCallbackIp();

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/getcallbackip', $result['api']);
    }

    /**
     * @return \Mockery\MockInterface|\EasyWeChat\Applications\OfficialAccount\Fundamental\Client
     */
    private function make($appId = 'wxappid')
    {
        $accessToken = new AccessToken($appId);
        $api = \Mockery::mock('EasyWeChat\Applications\OfficialAccount\Fundamental\Client[parseJSON]', [$accessToken]);
        $api->shouldReceive('parseJSON')->andReturnUsing(function ($api, $params) {
            if (isset($params[1])) {
                return ['api' => $params[0], 'params' => $params[1]];
            }

            return ['api' => $params[0]];
        });

        return $api;
    }
}
