<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) AHai <ahai@loveinin.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Tag.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    AHai <ahai@loveinin.com>
 * @copyright 2016 AHai <ahai@loveinin.com>
 *
 * @link      https://github.com/dongnanyanhai
 */

namespace Overtrue\Wechat;

/**
 * 用户标签.
 */
class Tag
{

    const API_GET                = 'https://api.weixin.qq.com/cgi-bin/tags/get';
    const API_CREATE             = 'https://api.weixin.qq.com/cgi-bin/tags/create';
    const API_UPDATE             = 'https://api.weixin.qq.com/cgi-bin/tags/update';
    const API_DELETE             = 'https://api.weixin.qq.com/cgi-bin/tags/delete';
    const API_USER_TAGS          = 'https://api.weixin.qq.com/cgi-bin/tags/getidlist';
    const API_MEMBER_BATCH_TAG   = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging';
    const API_MEMBER_BATCH_UNTAG = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging';
    const API_USERS_OF_TAG       = 'https://api.weixin.qq.com/cgi-bin/user/tag/get';

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->http = new Http(new AccessToken($appId, $appSecret));
    }

    /**
     * 创建标签.
     *
     * @param string $name
     *
     * @return int
     */
    public function create($name)
    {
        $params = array(
            'tag' => array('name' => $name),
        );

        $response = $this->http->jsonPost(self::API_CREATE, $params);

        return $response['tag'];
    }

    /**
     * 获取所有标签.
     *
     * @return array
     */
    public function lists()
    {
        $response = $this->http->get(self::API_GET);

        return $response['tags'];
    }

    /**
     * 更新组名称.
     *
     * @param int    $tagId
     * @param string $name
     *
     * @return bool
     */
    public function update($tagId, $name)
    {
        $params = array(
            'tag' => array(
                'id'   => $tagId,
                'name' => $name,
            ),
        );

        return $this->http->jsonPost(self::API_UPDATE, $params);
    }

    /**
     * 删除标签.
     *
     * @param int $tagId
     *
     * @return bool
     */
    public function delete($tagId)
    {
        $params = array(
            'tag' => array(
                'id' => $tagId,
            ),
        );

        return $this->http->jsonPost(self::API_DELETE, $params);
    }

    /**
     * 获取用户所在标签.
     *
     * @param string $openId
     *
     * @return int
     */
    public function userTags($openId)
    {
        $params = array('openid' => $openId);

        $response = $this->http->jsonPost(self::API_USER_TAGS, $params);

        return $response['tagid_list'];
    }


    /**
     * 获取标签组全部用户.
     *
     * @param string $tagId
     *
     * @return int
     */
    public function usersOfTag($tagId)
    {
        $params = array('tagid' => $tagId);

        $response = $this->http->jsonPost(self::API_USERS_OF_TAG, $params);

        return $response;
    }

    /**
     * 批量增加标签.
     *
     * @param array $openIds
     * @param int   $tagId
     *
     * @return bool
     */
    public function batchTagUsers(array $openIds, $tagId)
    {
        $params = array(
                   'openid_list' => $openIds,
                   'tagid' => $tagId,
                  );

        $response = $this->http->jsonPost(self::API_MEMBER_BATCH_TAG, $params);

        return $response;
    }

    /**
     * 批量取消标签.
     *
     * @param array $openIds
     * @param int   $tagId
     *
     * @return bool
     */
    public function batchUntagUsers(array $openIds, $tagId)
    {
        $params = array(
                   'openid_list' => $openIds,
                   'tagid' => $tagId,
                  );

        $response = $this->http->jsonPost(self::API_MEMBER_BATCH_UNTAG, $params);

        return $response;

    }
}
