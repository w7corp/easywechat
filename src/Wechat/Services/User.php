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

        return new Bag($this->getRequest(self::API_GET, $params));
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

        return new Bag($this->getRequest(self::API_LIST, $params));
    }

    /**
     * 修改用户备注
     *
     * @param string $openId
     * @param string $remark 备注
     *
     * @return boolean
     */
    public function remark($openId, $remark)
    {
        $params = array(
                   'openid' => $openId,
                   'remark' => $remark,
                  );

        return $this->postRequest(self::API_REMARK, $params);
    }

    /**
     * 获取用户所在分组
     *
     * @param string $openId
     *
     * @return string
     */
    public function group($openId)
    {
        return $this->getGroup($openId);
    }

    /**
     * 获取用户所在的组
     *
     * @param string $openId
     *
     * @return integer
     */
    public function getGroup($openId)
    {
        $params = array(
                   'openid' => $this->openId,
                  );

        $response = $this->postRequest(self::API_GROUP, $params);

        return $response['groupid'];
    }
}