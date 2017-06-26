<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\MiniProgram\Sns;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * JsCode 2 session key.
     *
     * @param string $jsCode
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Support\Collection|array|object|string
     */
    public function getSessionKey($jsCode)
    {
        $params = [
            'appid' => $this->config['app_id'],
            'secret' => $this->config['secret'],
            'js_code' => $jsCode,
            'grant_type' => 'authorization_code',
        ];

        return $this->httpGet('sns/jscode2session', $params);
    }
}
