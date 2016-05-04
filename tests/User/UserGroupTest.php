<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\User\Group;

class UserGroupTest extends TestCase
{
    public function getGroup()
    {
        $group = Mockery::mock('EasyWeChat\User\Group[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $group->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $group;
    }

    /**
     * Test create().
     */
    public function testCreate()
    {
        $group = $this->getGroup();

        $result = $group->create('overtrue');

        $this->assertStringStartsWith(Group::API_CREATE, $result['api']);
        $this->assertEquals(['name' => 'overtrue'], $result['params']['group']);
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $group = $this->getGroup();

        $result = $group->lists();

        $this->assertStringStartsWith(Group::API_GET, $result['api']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $group = $this->getGroup();

        $expected = [
            'group' => [
                'id' => 12,
                'name' => 'newName',
            ],
        ];

        $result = $group->update(12, 'newName');

        $this->assertStringStartsWith(Group::API_UPDATE, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $expected = [
            'group' => [
                'id' => 12,
            ],
        ];

        $group = $this->getGroup();
        $result = $group->delete(12);

        $this->assertStringStartsWith(Group::API_DELETE, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test userGroup().
     */
    public function testUserGroup()
    {
        $group = $this->getGroup();

        $result = $group->userGroup('overtrue');

        $this->assertStringStartsWith(Group::API_USER_GROUP_ID, $result['api']);
        $this->assertEquals(['openid' => 'overtrue'], $result['params']);
    }

    /**
     * Test moveUser().
     */
    public function testMoveUser()
    {
        $group = $this->getGroup();

        $expected = [
            'openid' => 'overtrue',
            'to_groupid' => 13,
        ];

        $result = $group->moveUser('overtrue', 13);

        $this->assertStringStartsWith(Group::API_MEMBER_UPDATE, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }

    /**
     * Test moveUsers().
     */
    public function testMoveUsers()
    {
        $group = $this->getGroup();

        $expected = [
            'openid_list' => ['overtrue', 'foobar'],
            'to_groupid' => 13,
        ];

        $result = $group->moveUsers(['overtrue', 'foobar'], 13);

        $this->assertStringStartsWith(Group::API_MEMBER_BATCH_UPDATE, $result['api']);
        $this->assertEquals($expected, $result['params']);
    }
}
