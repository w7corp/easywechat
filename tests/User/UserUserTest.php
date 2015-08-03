<?php

use EasyWeChat\User\User;

class UserUserTest extends TestCase
{
    /**
     * Test get().
     */
    public function testGet()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturnUsing(function ($api, $params) {
            return [$params['openid'] => ['overtrue']];
        });

        $user = Mockery::mock('EasyWeChat\User\User', [$http])->makePartial();
        $user->shouldReceive('lists')->andReturn(['foo', 'bar']);

        $result = $user->get();// return lists();

        $this->assertEquals(['foo', 'bar'], $result);

        $result = $user->get('openid_fo_overtrue');

        $this->assertEquals(['overtrue'], $result->openid_fo_overtrue);
    }

    /**
     * Test batchGet().
     */
    public function testBatchGet()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $user = new User($http);

        $result = $user->batchGet(['openid1', 'openid2']);

        $expected = [
            [
                'openid' => 'openid1',
                'lang' => 'zh_CN',
            ],
            [
                'openid' => 'openid2',
                'lang' => 'zh_CN',
            ],
        ];

        $this->assertEquals($expected, $result->user_list);
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $user = new User($http);

        $result = $user->lists('openid1');

        $this->assertEquals('openid1', $result->next_openid);
    }

    /**
     * Test remark().
     */
    public function testRemark()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return $params;
        });

        $user = new User($http);

        $result = $user->remark('openid1', 'easywechat');

        $this->assertEquals('easywechat', $result['remark']);
    }

    /**
     * Test group().
     */
    public function testGroup()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);

        $user = Mockery::mock('EasyWeChat\User\User', [$http])->makePartial();
        $user->shouldReceive('getGroup')->andReturn('foo');

        $result = $user->group('openid1');

        $this->assertEquals('foo', $result);
    }

    /**
     * Test getGroup().
     */
    public function testGetGroup()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $params) {
            return ['groupid' => 123456];
        });

        $user = new User($http);

        $result = $user->getGroup('openid1');

        $this->assertEquals('123456', $result);
    }
}
