<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OfficialAccount\CustomerService;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class SessionClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class SessionClient extends BaseClient
{
    const API_CREATE = 'https://api.weixin.qq.com/customservice/kfsession/create';
    const API_CLOSE = 'https://api.weixin.qq.com/customservice/kfsession/close';
    const API_GET = 'https://api.weixin.qq.com/customservice/kfsession/getsession';
    const API_LISTS = 'https://api.weixin.qq.com/customservice/kfsession/getsessionlist';
    const API_WAITERS = 'https://api.weixin.qq.com/customservice/kfsession/getwaitcase';

    /**
     * List all sessions of $account.
     *
     * @param string $account
     *
     * @return mixed
     */
    public function lists($account)
    {
        return $this->httpGet(self::API_LISTS, ['kf_account' => $account]);
    }

    /**
     * List all waiters of $account.
     *
     * @return mixed
     */
    public function waiters()
    {
        return $this->httpGet(self::API_WAITERS);
    }

    /**
     * Create a session.
     *
     * @param string $account
     * @param string $openId
     *
     * @return mixed
     */
    public function create($account, $openId)
    {
        $params = [
            'kf_account' => $account,
            'openid' => $openId,
        ];

        return $this->httpPostJson(self::API_CREATE, $params);
    }

    /**
     * Close a session.
     *
     * @param string $account
     * @param string $openId
     *
     * @return mixed
     */
    public function close($account, $openId)
    {
        $params = [
            'kf_account' => $account,
            'openid' => $openId,
        ];

        return $this->httpPostJson(self::API_CLOSE, $params);
    }

    /**
     * Get a session.
     *
     * @param string $openId
     *
     * @return mixed
     */
    public function get($openId)
    {
        return $this->httpGet(self::API_GET, ['openid' => $openId]);
    }
}
