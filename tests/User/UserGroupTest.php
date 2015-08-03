<?php

use EasyWeChat\User\Group;

class UserGroupTest extends TestCase
{
    /**
     * Test create()
     */
    public function testCreate()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $user = new Group($http);

        $this->assertEquals(['name' => 'overtrue'], $user->create('overtrue'));
    }

    /**
     * Test lists()
     */
    public function testLists()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturnUsing(function(){
            return ['groups' => ['foo', 'bar']];
        });

        $user = new Group($http);

        $this->assertEquals(['foo', 'bar'], $user->lists());
    }

    /**
     * Test update()
     */
    public function testUpdate()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $expected = [
            'group' => [
                'id'   => 12,
                'name' => 'newName',
            ],
        ];

        $user = new Group($http);

        $this->assertEquals($expected, $user->update(12, 'newName'));
    }

    /**
     * Test delete()
     */
    public function testDelete()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $expected = [
            'group' => [
                'id'   => 12,
            ],
        ];

        $user = new Group($http);

        $this->assertEquals($expected, $user->delete(12));
    }

    /**
     * Test userGroup()
     */
    public function testUserGroup()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return ['groupid' => $params];
        });

        $user = new Group($http);

        $this->assertEquals(['openid' => 'overtrue'], $user->userGroup('overtrue'));
    }

    /**
     * Test moveUser()
     */
    public function testMoveUser()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $user = new Group($http);

        $expected = [
            'openid' => 'overtrue',
            'to_groupid' => 13,
        ];

        $this->assertEquals($expected, $user->moveUser('overtrue', 13));
    }

    /**
     * Test moveUsers()
     */
    public function testMoveUsers()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $user = new Group($http);

        $expected = [
            'openid_list' => ['overtrue', 'foobar'],
            'to_groupid' => 13,
        ];

        $this->assertEquals($expected, $user->moveUsers(['overtrue','foobar'], 13));
    }
}