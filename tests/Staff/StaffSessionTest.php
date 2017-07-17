<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Staff;

use EasyWeChat\Staff\Session;
use EasyWeChat\Tests\TestCase;

class StaffSessionTest extends TestCase
{
    public function getSession()
    {
        $session = \Mockery::mock('EasyWeChat\Staff\Session[parseJSON]', [\Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $session->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
                'quires' => empty($params[3]) ? null : $params[3],
            ];
        });

        return $session;
    }

    /**
     * Test lists($account).
     */
    public function testLists()
    {
        $session = $this->getSession();

        $response = $session->lists('foo');

        $this->assertStringStartsWith(Session::API_LISTS, $response['api']);
        $this->assertEquals('foo', $response['params']['kf_account']);
    }

    /**
     * Test waiters($account).
     */
    public function testWaiters()
    {
        $session = $this->getSession();

        $response = $session->waiters();

        $this->assertStringStartsWith(Session::API_WAITERS, $response['api']);
        $this->assertEmpty($response['params']);
    }

    /**
     * Test create($account, $openID).
     */
    public function testCreate()
    {
        $session = $this->getSession();

        $response = $session->create('anzhengchao@test', 'overtrue_openid');

        $this->assertStringStartsWith(Session::API_CREATE, $response['api']);
        $this->assertEquals('anzhengchao@test', $response['params']['kf_account']);
        $this->assertEquals('overtrue_openid', $response['params']['openid']);
    }

    /**
     * Test close($account, $openId).
     */
    public function testClose()
    {
        $session = $this->getSession();

        $response = $session->close('anzhengchao@test', 'overtrue_openid');

        $this->assertStringStartsWith(Session::API_CLOSE, $response['api']);
        $this->assertEquals('anzhengchao@test', $response['params']['kf_account']);
        $this->assertEquals('overtrue_openid', $response['params']['openid']);
    }

    /**
     * Test get($openId).
     */
    public function testGet()
    {
        $session = $this->getSession();

        $response = $session->get('mock_openid');

        $this->assertStringStartsWith(Session::API_GET, $response['api']);
        $this->assertEquals('mock_openid', $response['params']['openid']);
    }
}
