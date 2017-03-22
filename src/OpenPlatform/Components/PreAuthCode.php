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
 * PreAuthCode.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    mingyoung <mingyoungcheung@gmail.com>
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform\Components;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Support\Url;

class PreAuthCode extends AbstractComponent
{
    /**
     * Create pre auth code url.
     */
    const CREATE_PRE_AUTH_CODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';

    /**
     * Pre auth link.
     */
    const PRE_AUTH_LINK = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';

    /**
     * Redirect Uri.
     *
     * @var string
     */
    protected $redirectUri;

    /**
     * Get pre auth code.
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function getCode()
    {
        $data = [
            'component_appid' => $this->getAppId(),
        ];

        $result = $this->parseJSON('json', [self::CREATE_PRE_AUTH_CODE, $data]);

        if (empty($result['pre_auth_code'])) {
            throw new InvalidArgumentException('Invalid response.');
        }

        return $result['pre_auth_code'];
    }

    /**
     * Get Redirect url.
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getRedirectUri()
    {
        if (!$this->redirectUri) {
            throw new RuntimeException('You need to provided a redirect uri.');
        }

        return $this->redirectUri;
    }

    /**
     * Set redirect uri.
     *
     * @param string $uri
     *
     * @return $this
     */
    public function setRedirectUri($uri)
    {
        $this->redirectUri = $uri;

        return $this;
    }

    /**
     * Get auth page link.
     *
     * @return string
     */
    public function getAuthLink()
    {
        return sprintf(self::PRE_AUTH_LINK,
            $this->getAppId(),
            $this->getCode(),
            urlencode($this->getRedirectUri())
        );
    }
}
