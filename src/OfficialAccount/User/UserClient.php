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
     * @param string $openid
     * @param string $lang
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get(string $openid, string $lang = 'zh_CN')
    {
        $params = [
            'openid' => $openid,
            'lang' => $lang,
        ];

        return $this->httpGet('cgi-bin/user/info', $params);
    }

    /**
     * Batch get users.
     *
     * @param array  $openids
     * @param string $lang
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function select(array $openids, string $lang = 'zh_CN')
    {
        return $this->httpPostJson('cgi-bin/user/info/batchget', [
            'user_list' => array_map(function ($openid) use ($lang) {
                return [
                    'openid' => $openid,
                    'lang' => $lang,
                ];
            }, $openids),
        ]);
    }

    /**
     * List users.
     *
     * @param string $nextOpenId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function list(string $nextOpenId = null)
    {
        $params = ['next_openid' => $nextOpenId];

        return $this->httpGet('cgi-bin/user/get', $params);
    }

    /**
     * Set user remark.
     *
     * @param string $openid
     * @param string $remark
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function remark(string $openid, string $remark)
    {
        $params = [
            'openid' => $openid,
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
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function blacklist(string $beginOpenid = null)
    {
        $params = ['begin_openid' => $beginOpenid];

        return $this->httpPostJson('cgi-bin/tags/members/getblacklist', $params);
    }

    /**
     * Batch block user.
     *
     * @param array|string $openidList
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function block($openidList)
    {
        $params = ['openid_list' => (array) $openidList];

        return $this->httpPostJson('cgi-bin/tags/members/batchblacklist', $params);
    }

    /**
     * Batch unblock user.
     *
     * @param array $openidList
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function unblock($openidList)
    {
        $params = ['openid_list' => (array) $openidList];

        return $this->httpPostJson('cgi-bin/tags/members/batchunblacklist', $params);
    }

    /**
     * @param string $oldAppId
     * @param array  $openidList
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function changeOpenid(string $oldAppId, array $openidList)
    {
        $params = [
            'from_appid' => $oldAppId,
            'openid_list' => $openidList,
        ];

        return $this->httpPostJson('cgi-bin/changeopenid', $params);
    }
}
