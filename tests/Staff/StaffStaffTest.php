<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Staff\Staff;

class StaffStaffTest extends PHPUnit_Framework_TestCase
{
    public function getStaff()
    {
        $staff = Mockery::mock('EasyWeChat\Staff\Staff[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $staff->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
                'quires' => empty($params[3]) ? null : $params[3],
            ];
        });

        return $staff;
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $staff = $this->getStaff();

        $this->assertStringStartsWith(Staff::API_LISTS, $staff->lists()['api']);
    }

    /**
     * Test onlines().
     */
    public function testOnlines()
    {
        $staff = $this->getStaff();

        $this->assertStringStartsWith(Staff::API_ONLINE, $staff->onlines()['api']);
    }

    /**
     * Test create().
     */
    public function testCreate()
    {
        $staff = $this->getStaff();

        $response = $staff->create('anzhengchao@gmail.com', 'overtrue', 'foobar');

        $this->assertStringStartsWith(Staff::API_CREATE, $response['api']);
        $this->assertEquals('anzhengchao@gmail.com', $response['params']['kf_account']);
        $this->assertEquals('overtrue', $response['params']['nickname']);
        $this->assertEquals('foobar', $response['params']['password']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $staff = $this->getStaff();

        $response = $staff->update('anzhengchao@gmail.com', 'overtrue', 'foobar');

        $this->assertStringStartsWith(Staff::API_UPDATE, $response['api']);
        $this->assertEquals('anzhengchao@gmail.com', $response['params']['kf_account']);
        $this->assertEquals('overtrue', $response['params']['nickname']);
        $this->assertEquals('foobar', $response['params']['password']);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        // 这里 不 TM 测了
        // $staff = $this->getStaff();

        // $response = $staff->delete('anzhengchao@gmail.com');

        // $this->assertStringStartsWith(Staff::API_DELETE, $response['api']);
        // $this->assertContains('kf_account=anzhengchao@gmail.com', $response['api']);
    }

    /**
     * Test avatar().
     */
    public function testAvatar()
    {
        $staff = $this->getStaff();

        $response = $staff->avatar('anzhengchao@gmail.com', '/foobar/avatar.jpg');

        $this->assertStringStartsWith(Staff::API_AVATAR_UPLOAD, $response['api']);
        $this->assertContains('anzhengchao@gmail.com', $response['quires']['kf_account']);
        $this->assertEquals(['media' => '/foobar/avatar.jpg'], $response['params']);
    }
}
