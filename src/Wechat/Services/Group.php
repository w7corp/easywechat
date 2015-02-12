<?php

namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;
use Overtrue\Wechat\Utils\Bag;

class Group
{
    const API_GET                 = 'https://api.weixin.qq.com/cgi-bin/groups/get';
    const API_CREATE              = 'https://api.weixin.qq.com/cgi-bin/groups/create';
    const API_UPDATE              = 'https://api.weixin.qq.com/cgi-bin/groups/update';
    const API_USER_GROUP_ID       = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const API_MEMBER_UPDATE       = 'https://api.weixin.qq.com/cgi-bin/groups/members/update';
    const API_MEMBER_BATCH_UPDATE = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate';

    /**
     * 当前服务使用json方式请求
     *
     * @var array
     */
    protected $headers = array('content-type:application/json');

    /**
     * 创建分组
     *
     * @param string $name
     *
     * @return integer
     */
    public function create($name)
    {
        $params = array(
                    'group' => array(
                                'name' => $name,
                               ),
                  );

        $response = Wechat::request('POST', self::API_CREATE, $params);

        return $response['group'];
    }

    /**
     * 获取所有分组
     *
     * @return array
     */
    public function all()
    {
        $response = Wechat::request('GET', self::API_GET);

        return $response['groups'];
    }

    /**
     * 更新组名称
     *
     * @param integer $groupId
     * @param string  $name
     *
     * @return Overtrue\Wechat\Group
     */
    public function update($groupId, $name)
    {
        $params = array(
                    'group' => array(
                                'id'   => $groupId,
                                'name' => $name,
                               ),
                  );

        Wechat::request('POST', self::API_UPDATE, $params);

        return true;
    }

    /**
     * 获取用户所在分组
     *
     * @param string $openId
     *
     * @return integer
     */
    public function userGroup($openId)
    {
         $params = array(
                    'openid' => $openId,
                  );

        $response = Wechat::request('POST', self::API_USER_GROUP_ID, $params);

        return $response['groupid'];
    }

    /**
     * 移动单个用户
     *
     * @param string  $openId
     * @param integer $groupId
     *
     * @return boolean
     */
    public function moveUser($openId, $groupId)
    {
        $params = array(
                   'openid'     => $openId,
                   'to_groupid' => $groupId,
                  );

        Wechat::request('POST', self::API_MEMBER_UPDATE, $params);

        return true;
    }

    /**
     * 批量移动用户
     *
     * @param array   $openIds
     * @param integer $groupId
     *
     * @return boolean
     */
    public function moveUsers(array $openIds, $groupId)
    {
        $params = array(
                   'openid_list' => $openIds,
                   'to_groupid'  => $groupId,
                  );

        Wechat::request('POST', self::API_MEMBER_BATCH_UPDATE, $params);

        return true;
    }
}