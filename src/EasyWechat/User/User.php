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
 * Class User.
 */
class User
{
    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    const API_GET = 'https://api.weixin.qq.com/cgi-bin/user/info';
    const API_BATCH_GET = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget';
    const API_LIST = 'https://api.weixin.qq.com/cgi-bin/user/get';
    const API_GROUP = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
    const API_REMARK = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark';
    const API_OAUTH_GET = 'https://api.weixin.qq.com/sns/userinfo';

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
     * Fetch a user by open id.
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
     * Batch get users.
     *
     * @param array  $openIds
     * @param string $lang
     *
     * @return array
     */
    public function batchGet(array $openIds, $lang = 'zh_CN')
    {
        $params = [];

        $params['user_list'] = array_map(function ($openId) use ($lang) {
            return [
                    'openid' => $openId,
                    'lang' => $lang,
                    ];
        }, $openIds);

        return new Collection($this->http->get(self::API_BATCH_GET, $params));
    }

    /**
     * List users.
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
     * Set user remark.
     *
     * @param string $openId
     * @param string $remark
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
     * Get user's group id.
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
     * Get user's group id.
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
}//end class

