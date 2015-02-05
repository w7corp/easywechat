<?php

namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;
use Overtrue\Wechat\Utils\Bag;

class Group extends Service
{

    const API_CREATE              = 'https://api.weixin.qq.com/cgi-bin/groups/create';
    const API_UPDATE              = 'https://api.weixin.qq.com/cgi-bin/groups/update';
    const API_GET                 = 'https://api.weixin.qq.com/cgi-bin/groups/get';
    const API_MEMBER_UPDATE       = 'https://api.weixin.qq.com/cgi-bin/groups/members/update';
    const API_MEMBER_BATCH_UPDATE = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate';

    /**
     * 创建分组
     *
     * @param string $name
     *
     * @return Overtrue\Wechat\Group
     */
    public function create($name)
    {
        $params = array(
                    'group' => array(
                                'name' => $name,
                               ),
                  );

        $response = $this->postRequest(self::API_CREATE, $params);

        return $response['group'];
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

        $this->postRequest(self::API_UPDATE, $params);

        return true;
    }

    /**
     * 移动单个用户
     *
     * @param integer $groupId
     * @param string  $openId
     *
     * @return boolean
     */
    public function user($groupId, $openId)
    {
        $params = array(
                   'openid'     => $openId,
                   'to_groupid' => $groupId,
                  );

        $this->postRequest(self::API_MEMBER_UPDATE, $params);

        return true;
    }

    /**
     * 批量移动用户
     *
     * @param integer $groupId
     * @param array   $openIds
     *
     * @return boolean
     */
    public function users($groupId, array $openIds)
    {
        $params = array(
                   'openid_list' => $openIds,
                   'to_groupid'  => $groupId,
                  );

        $this->postRequest(self::API_MEMBER_BATCH_UPDATE, $params);

        return true;
    }
}