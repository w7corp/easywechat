<?php

namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;
use Overtrue\Wechat\Utils\Bag;

class User extends Service
{
    const API_GET       = 'https://api.weixin.qq.com/cgi-bin/user/info';
    const API_LIST      = 'https://api.weixin.qq.com/cgi-bin/user/get';
    const API_GROUP     = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const API_REMARK    = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark';
    const API_OAUTH_GET = 'https://api.weixin.qq.com/sns/userinfo';

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

        return new Bag($this->request('GET', self::API_GET, $params));
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

        return new Bag($this->request('GET', self::API_LIST, $params));
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

        return $this->request('POST', self::API_REMARK, $params);
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
        return $this->getGroup();
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

        $response = $this->request('POST', self::API_GROUP, $params);

        return $response['groupid'];
    }
}