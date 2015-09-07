<?php

use EasyWeChat\Core\Http;
use EasyWeChat\Staff\Staff;

class StaffStaffTest extends TestCase
{
    public function getHttp()
    {
        $http = Mockery::mock(Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);

        return $http;
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $http = $this->getHttp();
        $http->shouldReceive('get')->andReturnUsing(function ($api) {
            return ['kf_list' => $api];
        });
        $staff = new Staff($http);

        $this->assertEquals(Staff::API_LISTS, $staff->lists());
    }

    /**
     * Test onlines().
     */
    public function testOnlines()
    {
        $http = $this->getHttp();
        $http->shouldReceive('get')->andReturnUsing(function ($api) {
            return ['kf_online_list' => $api];
        });
        $staff = new Staff($http);

        $this->assertEquals(Staff::API_ONLINE, $staff->onlines());
    }

    /**
     * Test create().
     */
    public function testCreate()
    {
        $http = $this->getHttp();
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return compact('api', 'params');
        });
        $staff = new Staff($http);

        $response = $staff->create('anzhengchao@gmail.com', 'overtrue', 'foobar');

        $this->assertEquals(Staff::API_CREATE, $response['api']);
        $this->assertEquals('anzhengchao@gmail.com', $response['params']['kf_account']);
        $this->assertEquals('overtrue', $response['params']['nickname']);
        $this->assertEquals('foobar', $response['params']['password']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $http = $this->getHttp();
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return compact('api', 'params');
        });
        $staff = new Staff($http);

        $response = $staff->update('anzhengchao@gmail.com', 'overtrue', 'foobar');

        $this->assertEquals(Staff::API_UPDATE, $response['api']);
        $this->assertEquals('anzhengchao@gmail.com', $response['params']['kf_account']);
        $this->assertEquals('overtrue', $response['params']['nickname']);
        $this->assertEquals('foobar', $response['params']['password']);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $http = $this->getHttp();
        $http->shouldReceive('get')->andReturnUsing(function ($api, $params) {
            return compact('api', 'params');
        });
        $staff = new Staff($http);

        $response = $staff->delete('anzhengchao@gmail.com');

        $this->assertEquals(Staff::API_DELETE, $response['api']);
        $this->assertEquals('anzhengchao@gmail.com', $response['params']['kf_account']);
    }

    /**
     * Test avatar().
     */
    public function testAvatar()
    {
        $http = $this->getHttp();
        $http->shouldReceive('upload')->andReturnUsing(function ($api, $media, $params) {
            return compact('api', 'media', 'params');
        });
        $staff = new Staff($http);

        $response = $staff->avatar('anzhengchao@gmail.com', '/foobar/avatar.jpg');

        $this->assertEquals(Staff::API_AVATAR_UPLOAD, $response['api']);
        $this->assertEquals('anzhengchao@gmail.com', $response['params']['kf_account']);
        $this->assertEquals(['media' => '/foobar/avatar.jpg'], $response['media']);
    }
}
