<?php

/**
 * User.php.
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

use EasyWeChat\Support\Collection;

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
    const API_LIST = 'https://api.weixin.qq.com/cgi-bin/user/get';
    const API_GROUP = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const API_REMARK = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark';
    const API_OAUTH_GET = 'https://api.weixin.qq.com/sns/userinfo';

    /**
     * constructor.
     *
     * <pre>
     * $config:
     *
     * array(
     *  'app_id' => YOUR_APPID,  // string mandatory;
     *  'secret' => YOUR_SECRET, // string mandatory;
     * )
     * </pre>
     *
     * @param array $config configuration array
     */
    public function __construct(array $config)
    {
        $this->http = new Http(new AccessToken($config));
    }

    /**
     * 读取用户信息.
     *
     * @param string $openId
     * @param string $lang
     *
     * @return Collection
     */
    public function get($openId = null, $lang = 'zh_CN')
    {
        if (empty($openId)) {
            return $this->lists();
        }

        $params = [
                   'openid' => $openId,
                   'lang' => $lang,
                  ];

        return new Collection($this->http->get(self::API_GET, $params));
    }

    /**
     * 获取用户列表.
     *
     * @param string $nextOpenId
     *
     * @return Collection
     */
    public function lists($nextOpenId = null)
    {
        $params = ['next_openid' => $nextOpenId];

        return new Collection($this->http->get(self::API_LIST, $params));
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
        $params = [
                   'openid' => $openId,
                   'remark' => $remark,
                  ];

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
        $params = ['openid' => $openId];

        $response = $this->http->jsonPost(self::API_GROUP, $params);

        return $response['groupid'];
    }
}
