<?php

use EasyWeChat\Core\Http;
use EasyWeChat\Staff\Manager;

class StaffManagerTest extends TestCase
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
        $manager = new Manager($http);

        $this->assertEquals(Manager::API_LISTS, $manager->lists());
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
        $manager = new Manager($http);

        $this->assertEquals(Manager::API_ONLINE, $manager->onlines());
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
        $manager = new Manager($http);

        $response = $manager->create('anzhengchao@gmail.com', 'overtrue', 'foobar');

        $this->assertEquals(Manager::API_CREATE, $response['api']);
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
        $manager = new Manager($http);

        $response = $manager->update('anzhengchao@gmail.com', 'overtrue', 'foobar');

        $this->assertEquals(Manager::API_UPDATE, $response['api']);
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
        $manager = new Manager($http);

        $response = $manager->delete('anzhengchao@gmail.com');

        $this->assertEquals(Manager::API_DELETE, $response['api']);
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
        $manager = new Manager($http);

        $response = $manager->avatar('anzhengchao@gmail.com', '/foobar/avatar.jpg');

        $this->assertEquals(Manager::API_AVATAR_UPLOAD, $response['api']);
        $this->assertEquals('anzhengchao@gmail.com', $response['params']['kf_account']);
        $this->assertEquals(['media' => '/foobar/avatar.jpg'], $response['media']);
    }
}
