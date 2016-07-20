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
 * Session.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */
namespace EasyWeChat\Staff;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class Session.
 */
class Session extends AbstractAPI
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
     * @return array
     */
    public function lists($account)
    {
        return $this->parseJSON('get', [self::API_LISTS, ['kf_account' => $account]]);
    }

    /**
     * List all waiters of $account.
     *
     * @return array
     */
    public function waiters()
    {
        return $this->parseJSON('get', [self::API_WAITERS]);
    }

    /**
     * Create a session.
     *
     * @param string $account
     * @param string $openid
     *
     * @return bool
     */
    public function create($account, $openId)
    {
        $params = [
                   'kf_account' => $account,
                   'openid' => $openId,
                  ];

        return $this->parseJSON('json', [self::API_CREATE, $params]);
    }

    /**
     * Close a session.
     *
     * @param string $account
     * @param string $openId
     *
     * @return bool
     */
    public function close($account, $openId)
    {
        $params = [
                   'kf_account' => $account,
                   'openid' => $openId,
                  ];

        return $this->parseJSON('json', [self::API_CLOSE, $params]);
    }

    /**
     * Get a session.
     *
     * @param string $openId
     *
     * @return bool
     */
    public function get($openId)
    {
        return $this->parseJSON('get', [self::API_GET, ['openid' => $openId]]);
    }
}
