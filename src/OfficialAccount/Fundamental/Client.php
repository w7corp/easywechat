<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Fundamental;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Clear quota.
     *
     * @return mixed
     */
    public function clearQuota()
    {
        $params = [
            'appid' => $this->app['config']['app_id'],
        ];

        return $this->httpPostJson('cgi-bin/clear_quota', $params);
    }

    /**
     * Get wechat callback ip.
     *
     * @return mixed
     */
    public function getCallbackIp()
    {
        return $this->httpGet('cgi-bin/getcallbackip');
    }
}
