<?php

/**
 * Group.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\User;

/**
 * Class Group.
 */
class Group
{
    const API_GET = 'https://api.weixin.qq.com/cgi-bin/groups/get';
    const API_CREATE = 'https://api.weixin.qq.com/cgi-bin/groups/create';
    const API_UPDATE = 'https://api.weixin.qq.com/cgi-bin/groups/update';
    const API_DELETE = 'https://api.weixin.qq.com/cgi-bin/groups/delete';
    const API_USER_GROUP_ID = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const API_MEMBER_UPDATE = 'https://api.weixin.qq.com/cgi-bin/groups/members/update';
    const API_MEMBER_BATCH_UPDATE = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate';

    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Constructor.
     *
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http;
    }

    /**
     * Create group.
     *
     * @param string $name
     *
     * @return int
     */
    public function create($name)
    {
        $params = [
                   'group' => ['name' => $name],
                  ];

        $response = $this->http->jsonPost(self::API_CREATE, $params);

        return $response['group'];
    }

    /**
     * List all groups.
     *
     * @return array
     */
    public function lists()
    {
        $response = $this->http->get(self::API_GET);

        return $response['groups'];
    }

    /**
     * Update a group name.
     *
     * @param int    $groupId
     * @param string $name
     *
     * @return bool
     */
    public function update($groupId, $name)
    {
        $params = [
                   'group' => [
                               'id' => $groupId,
                               'name' => $name,
                              ],
                  ];

        return $this->http->jsonPost(self::API_UPDATE, $params);
    }

    /**
     * Delete group.
     *
     * @param int $groupId
     *
     * @return bool
     */
    public function delete($groupId)
    {
        $params = [
                   'group' => ['id' => $groupId],
                  ];

        return $this->http->jsonPost(self::API_DELETE, $params);
    }

    /**
     * Get user group.
     *
     * @param string $openId
     *
     * @return int
     */
    public function userGroup($openId)
    {
        $params = ['openid' => $openId];

        $response = $this->http->jsonPost(self::API_USER_GROUP_ID, $params);

        return $response['groupid'];
    }

    /**
     * Move user to a group.
     *
     * @param string $openId
     * @param int    $groupId
     *
     * @return bool
     */
    public function moveUser($openId, $groupId)
    {
        $params = [
                   'openid' => $openId,
                   'to_groupid' => $groupId,
                  ];

        $this->http->jsonPost(self::API_MEMBER_UPDATE, $params);

        return true;
    }

    /**
     * Batch move users to a group.
     *
     * @param array $openIds
     * @param int   $groupId
     *
     * @return bool
     */
    public function moveUsers(array $openIds, $groupId)
    {
        $params = [
                   'openid_list' => $openIds,
                   'to_groupid' => $groupId,
                  ];

        $this->http->jsonPost(self::API_MEMBER_BATCH_UPDATE, $params);

        return true;
    }
}//end class

