<?php

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

class User
{

    /**
     * 读取用户信息
     *
     * @param string $openId
     * @param string $lang
     *
     * @return array
     */
    public function get($openId, $lang = 'zh_CN')
    {
        $params = array(
                   'openid' => $openId,
                   'lang'   => $lang,
                  );

        return new Bag(Wechat::get(Wechat::makeUrl('user.get', $params)));
    }

    /**
     * 获取用户列表
     *
     * @param string $nextOpenId
     *
     * @return Overtrue\Wechat\Utils\Bag
     */
    public function users($nextOpenId = null)
    {
        $params = array('next_openid' => $nextOpenId);

        return new Bag(Wechat::get(Wechat::makeUrl('user.list', $params)));
    }

    /**
     * 修改用户备注
     *
     * @param string $id     open id
     * @param string $remark 备注
     *
     * @return boolean
     */
    public function remark($remark)
    {
        $params = array(
                   'openid' => $this->openId,
                   'remark' => $remark,
                  );

        return Wechat::post(Wechat::makeUrl('user.remark'), $params);
    }

    /**
     * 获取用户所在分组
     *
     * @param integer $groupId 是否返回group
     *
     * @return string
     */
    public function group()
    {
        return $groupId ? $this->moveTo($groupId) : $this->getGroup();
    }

    /**
     * 移动用户到分组
     *
     * @param integer $groupId
     *
     * @return boolean
     */
    public function toGroup($groupId)
    {
        $params = array(
                   'openid'     => $this->openId,
                   'to_groupid' => ($groupId instanceof Group) ? $groupId->id : $groupId;
                  );

        Wechat::post(Wechat::makeUrl('group.member.update'), $params);

        return true;
    }

    /**
     * 获取用户所在的组
     *
     * @return \Overtrue\Wechat\Group
     */
    public function getGroup()
    {
        $params = array(
                   'openid' => $this->openId,
                  );

        $response = Wechat::post(Wechat::makeUrl('user.group'), $params);

        return $response['groupid'];
    }
}