<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\User\User;

class UserUserTest extends TestCase
{
    public function getUser()
    {
        $user = Mockery::mock('EasyWeChat\User\User[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $user->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $user;
    }

    /**
     * Test get().
     */
    public function testGet()
    {
        $user = $this->getUser();

        $result = $user->get('openid_fo_overtrue');

        $this->assertStringStartsWith(User::API_GET, $result['api']);
        $this->assertEquals('openid_fo_overtrue', $result['params']['openid']);
        $this->assertEquals('zh_CN', $result['params']['lang']);
    }

    /**
     * Test batchGet().
     */
    public function testBatchGet()
    {
        $user = $this->getUser();

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

        $this->assertStringStartsWith(User::API_BATCH_GET, $result['api']);
        $this->assertEquals($expected, $result['params']['user_list']);
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $user = $this->getUser();

        $result = $user->lists('openid1');

        $this->assertStringStartsWith(User::API_LIST, $result['api']);
        $this->assertEquals('openid1', $result['params']['next_openid']);
    }

    /**
     * Test remark().
     */
    public function testRemark()
    {
        $user = $this->getUser();

        $result = $user->remark('openid1', 'easywechat');

        $this->assertStringStartsWith(User::API_REMARK, $result['api']);
        $this->assertEquals('openid1', $result['params']['openid']);
        $this->assertEquals('easywechat', $result['params']['remark']);
    }

    /**
     * Test group().
     */
    public function testGroup()
    {
        $user = $this->getUser();

        $result = $user->group('openid1');

        $this->assertEquals('openid1', $result['params']['openid']);

        $result = $user->getGroup('openid2');
        $this->assertEquals('openid2', $result['params']['openid']);
    }
}
