<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\User\Tag;

class UserTagTest extends TestCase
{
    public function getTag()
    {
        $tag = Mockery::mock('EasyWeChat\User\Tag[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);

        return $tag;
    }

    /**
     * Test create().
     */
    public function testCreate()
    {
        $tag = $this->getTag();

        $tag->shouldReceive('parseJSON')->withArgs(['json', [Tag::API_CREATE, ['tag' => ['name' => 'overtrue']]]])->once();

        $result = $tag->create('overtrue');
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $tag = $this->getTag();

        $tag->shouldReceive('parseJSON')->withArgs(['get', [Tag::API_GET]])->once();

        $result = $tag->lists();
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $tag = $this->getTag();

        $expected = [
            'tag' => [
                'id' => 12,
                'name' => 'newName',
            ],
        ];

        $tag->shouldReceive('parseJSON')->withArgs(['json', [Tag::API_UPDATE, $expected]])->once();

        $result = $tag->update(12, 'newName');
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $expected = [
            'tag' => [
                'id' => 12,
            ],
        ];

        $tag = $this->getTag();

        $tag->shouldReceive('parseJSON')->withArgs(['json', [Tag::API_DELETE, $expected]])->once();

        $result = $tag->delete(12);
    }

    /**
     * Test userTags().
     */
    public function testUserTags()
    {
        $tag = $this->getTag();

        $tag->shouldReceive('parseJSON')->withArgs(['json', [Tag::API_USER_TAGS, ['openid' => 'myopenid']]])->once();

        $result = $tag->userTags('myopenid');
    }

    /**
     * Test usersOfTag().
     */
    public function testUsersOfTag()
    {
        $tag = $this->getTag();

        $tag->shouldReceive('parseJSON')->withArgs(['json', [Tag::API_USERS_OF_TAG, ['tagid' => 'thetagid']]])->once();

        $result = $tag->usersOfTag('thetagid');
    }

    /**
     * Test batchTagUsers().
     */
    public function testBatchTagUsers()
    {
        $tag = $this->getTag();

        $params = [
            'openid_list' => [1, 2, 3],
            'tagid' => 'thetagid',
        ];

        $tag->shouldReceive('parseJSON')->withArgs(['json', [Tag::API_MEMBER_BATCH_TAG, $params]])->once();

        $result = $tag->batchTagUsers([1, 2, 3], 'thetagid');
    }

    /**
     * Test batchUntagUsers().
     */
    public function testBatchUntagUsers()
    {
        $tag = $this->getTag();

        $params = [
            'openid_list' => [1, 2, 3],
            'tagid' => 'thetagid',
        ];

        $tag->shouldReceive('parseJSON')->withArgs(['json', [Tag::API_MEMBER_BATCH_UNTAG, $params]])->once();

        $result = $tag->batchUntagUsers([1, 2, 3], 'thetagid');
    }
}
