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
 * MiniProgram.php.
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

namespace EasyWeChat\MiniProgram;

use EasyWeChat\Core\AbstractAPI;

/**
 * Class MiniProgram.
 */
class MiniProgram extends AbstractAPI
{
    /**
     * Api.
     */
    const JSCODE_TO_SESSION = 'https://api.weixin.qq.com/sns/jscode2session';

    /**
     * Mini program config.
     *
     * @var array
     */
    protected $config;

    /**
     * MiniProgram constructor.
     *
     * @param \EasyWeChat\MiniProgram\AccessToken $accessToken
     * @param array $config
     */
    public function __construct($accessToken, $config)
    {
        parent::__construct($accessToken);

        $this->config = $config;
    }

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
