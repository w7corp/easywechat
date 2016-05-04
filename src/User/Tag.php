<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Group.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\User;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class Tag.
 */
class Tag extends AbstractAPI
{
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/tags/get';
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/tags/create';
    const API_UPDATE = 'https://api.weixin.qq.com/cgi-bin/tags/update';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/tags/delete';
    const API_USER_TAGS = 'https://api.weixin.qq.com/cgi-bin/tags/getidlist';
    const API_MEMBER_BATCH_TAG = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging';
    const API_MEMBER_BATCH_UNTAG = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging';
    const API_USERS_OF_TAG = 'https://api.weixin.qq.com/cgi-bin/user/tag/get';

    /**
     * Create tag.
     *
     * @param string $name
     *
     * @return int
     */
    public function create($name)
    {
        $params = [
                   'tag' => ['name' => $name],
                  ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    /**
     * List all tags.
     *
     * @return array
     */
    public function lists()
    {
        return $this->parseJSON('get', [self::API_GET]);
    }

    /**
     * Update a tag name.
     *
     * @param int    $tagId
     * @param string $name
     *
     * @return bool
     */
    public function update($tagId, $name)
    {
        $params = [
                   'tag' => [
                               'id' => $tagId,
                               'name' => $name,
                              ],
                  ];

        return $this->parseJSON('json', [self::API_UPDATE, $params]);
    }

    /**
     * Delete tag.
     *
     * @param int $tagId
     *
     * @return bool
     */
    public function delete($tagId)
    {
        $params = [
                   'tag' => ['id' => $tagId],
                  ];

        return $this->parseJSON('json', [self::API_DELETE, $params]);
    }

    /**
     * Get user tags.
     *
     * @param string $openId
     *
     * @return int
     */
    public function userTags($openId)
    {
        $params = ['openid' => $openId];

        return $this->parseJSON('json', [self::API_USER_TAGS, $params]);
    }

    /**
     * Get users from a tag.
     *
     * @param string $tagId
     *
     * @return int
     */
    public function usersOfTag($tagId)
    {
        $params = ['tagid' => $tagId];

        return $this->parseJSON('json', [self::API_USERS_OF_TAG, $params]);
    }

    /**
     * Batch tag users.
     *
     * @param array $openIds
     * @param int   $tagId
     *
     * @return bool
     */
    public function batchTagUsers(array $openIds, $tagId)
    {
        $params = [
                   'openid_list' => $openIds,
                   'tagid' => $tagId,
                  ];

        return $this->parseJSON('json', [self::API_MEMBER_BATCH_TAG, $params]);
    }

    /**
     * Untag users from a tag.
     *
     * @param array $openIds
     * @param int   $tagId
     *
     * @return bool
     */
    public function batchUntagUsers(array $openIds, $tagId)
    {
        $params = [
                   'openid_list' => $openIds,
                   'tagid' => $tagId,
                  ];

        return $this->parseJSON('json', [self::API_MEMBER_BATCH_UNTAG, $params]);
    }
}
