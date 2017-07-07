<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\CustomerService;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class SessionClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class SessionClient extends BaseClient
{
    /**
     * List all sessions of $account.
     *
     * @param string $account
     *
     * @return mixed
     */
    public function lists($account)
    {
        return $this->httpGet('customservice/kfsession/getsessionlist', ['kf_account' => $account]);
    }

    /**
     * List all waiters of $account.
     *
     * @return mixed
     */
    public function waiters()
    {
        return $this->httpGet('customservice/kfsession/getwaitcase');
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

        return $this->httpPostJson('customservice/kfsession/create', $params);
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

        return $this->httpPostJson('customservice/kfsession/close', $params);
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
        return $this->httpGet('customservice/kfsession/getsession', ['openid' => $openId]);
    }
}
