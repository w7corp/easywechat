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
 * Broadcast.php.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\ClearQuota;

use EasyWeChat\Core\AbstractAPI;

class ClearQuota extends AbstractAPI
{
    const API_CLEAR = 'https://api.weixin.qq.com/cgi-bin/clear_quota';

    public function clear($appId = '')
    {
        // appId defaults to the current appId
        if(! $appId)
        {
            $appId = $this->getAccessToken()->getAppId();
        }

        $options['appid'] = $appId;

        return $this->parseJSON('json', [self::API_CLEAR, $options]);
    }
}
