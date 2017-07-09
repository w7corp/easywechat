<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\User;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class UserClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class UserClient extends BaseClient
{
    /**
     * Fetch a user by open id.
     *
     * @param string $openId
     * @param string $lang
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function get($openId, $lang = 'zh_CN')
    {
        $params = [
            'openid' => $openId,
            'lang' => $lang,
        ];

        return $this->httpGet('cgi-bin/user/info', $params);
    }

    /**
     * Batch get users.
     *
     * @param array  $openIds
     * @param string $lang
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
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

        return $this->httpPostJson('cgi-bin/user/info/batchget', $params);
    }

    /**
     * List users.
     *
     * @param string $nextOpenId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function lists($nextOpenId = null)
    {
        $params = ['next_openid' => $nextOpenId];

        return $this->httpGet('cgi-bin/user/get', $params);
    }

    /**
     * Set user remark.
     *
     * @param string $openId
     * @param string $remark
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function remark($openId, $remark)
    {
        $params = [
            'openid' => $openId,
            'remark' => $remark,
        ];

        return $this->httpPostJson('cgi-bin/user/info/updateremark', $params);
    }

    /**
     * Get black list.
     *
     * @param string|null $beginOpenid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function blacklist($beginOpenid = null)
    {
        $params = ['begin_openid' => $beginOpenid];

        return $this->httpPostJson('cgi-bin/tags/members/getblacklist', $params);
    }

    /**
     * Batch block user.
     *
     * @param array $openidList
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function batchBlock(array $openidList)
    {
        $params = ['openid_list' => $openidList];

        return $this->httpPostJson('cgi-bin/tags/members/batchblacklist', $params);
    }

    /**
     * Batch unblock user.
     *
     * @param array $openidList
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     */
    public function batchUnblock(array $openidList)
    {
        $params = ['openid_list' => $openidList];

        return $this->httpPostJson('cgi-bin/tags/members/batchunblacklist', $params);
    }
}
