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
 * Fundamental API.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2017
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\Fundamental;

use EasyWeChat\Core\AbstractAPI;

class API extends AbstractAPI
{
    const API_CLEAR_QUOTA = 'https://api.weixin.qq.com/cgi-bin/clear_quota';
    const API_CALLBACK_IP = 'https://api.weixin.qq.com/cgi-bin/getcallbackip';

    /**
     * Clear quota.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function clearQuota()
    {
        $appid = $this->getAccessToken()->getAppId();

        return $this->parseJSON('json', [self::API_CLEAR_QUOTA, compact('appid')]);
    }

    /**
     * Get wechat callback ip.
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getCallbackIp()
    {
        return $this->parseJSON('get', [self::API_CALLBACK_IP]);
    }
}
