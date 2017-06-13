<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\OpenPlatform\PreAuthorization;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client
{
    /**
     * Create pre auth code url.
     */
    const CREATE_PRE_AUTH_CODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';

    /**
     * @return mixed
     */
    public function createCode()
    {
        $data = [
            'component_appid' => $this->getClientId(),
        ];

        return $this->parseJSON('json', [self::CREATE_PRE_AUTH_CODE, $data]);
    }

    /**
     * Redirect to WeChat PreAuthorization page.
     *
     * @param string $to
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect(string $to)
    {
        $authCode = $this->createCode()['auth_code'];
        $url = sprintf(
            'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
            $this->getClientId(), $authCode, urlencode($to)
        );

        return new RedirectResponse($url);
    }
}
