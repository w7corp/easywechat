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
 * User.php.
 *
 * Part of Overtrue\Wechat.
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

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

/**
 * 用户.
 */
class User
{
    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    const API_GET = 'https://api.weixin.qq.com/cgi-bin/user/info';
    const API_BATCH_GET = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget';
    const API_LIST = 'https://api.weixin.qq.com/cgi-bin/user/get';
    const API_GROUP = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const API_REMARK = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark';

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
     * 读取用户信息.
     *
     * @param string $openId
     * @param string $lang
     *
     * @return Bag
     */
    public function get($openId = null, $lang = 'zh_CN')
    {
        if (empty($openId)) {
            return $this->lists();
        }

        $params = array(
                   'openid' => $openId,
                   'lang' => $lang,
                  );

        return new Bag($this->http->get(self::API_GET, $params));
    }

    /**
     * Batch get users.
     *
     * @param array  $openIds
     * @param string $lang
     *
     * @return array
     */
    public function batchGet(array $openIds, $lang = 'zh_CN')
    {
        $params = array();

        $params['user_list'] = array_map(function ($openId) use ($lang) {
            return array(
                    'openid' => $openId,
                    'lang' => $lang,
                    );
        }, $openIds);

        $response = $this->http->jsonPost(self::API_BATCH_GET, $params);

        return new Bag($response['user_info_list']);
    }

    /**
     * 获取用户列表.
     *
     * @param string $nextOpenId
     *
     * @return Bag
     */
    public function lists($nextOpenId = null)
    {
        $params = array('next_openid' => $nextOpenId);

        return new Bag($this->http->get(self::API_LIST, $params));
    }

    /**
     * 修改用户备注.
     *
     * @param string $openId
     * @param string $remark 备注
     *
     * @return bool
     */
    public function remark($openId, $remark)
    {
        $params = array(
                   'openid' => $openId,
                   'remark' => $remark,
                  );

        return $this->http->jsonPost(self::API_REMARK, $params);
    }

    /**
     * 获取用户所在分组.
     *
     * @param string $openId
     *
     * @return int
     */
    public function group($openId)
    {
        return $this->getGroup($openId);
    }

    /**
     * 获取用户所在的组.
     *
     * @param string $openId
     *
     * @return int
     */
    public function getGroup($openId)
    {
        $params = array('openid' => $openId);

        $response = $this->http->jsonPost(self::API_GROUP, $params);

        return $response['groupid'];
    }
}
