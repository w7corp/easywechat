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
 * User.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\MiniProgram\Sns;

use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

class Sns extends AbstractMiniProgram
{
    /**
     * Api.
     */
    const JSCODE_TO_SESSION = 'https://api.weixin.qq.com/sns/jscode2session';

    /**
     * JsCode 2 session key.
     *
     * @param string $jsCode
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function getSessionKey($jsCode)
    {
        $params = [
            'appid' => $this->config['app_id'],
            'secret' => $this->config['secret'],
            'js_code' => $jsCode,
            'grant_type' => 'authorization_code',
        ];

        return $this->parseJSON('GET', [self::JSCODE_TO_SESSION, $params]);
    }
}
