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
     * Pre auth link.
     */
    const PRE_AUTH_LINK = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';

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
     * @param string $url
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($url)
    {
        return new RedirectResponse(
            sprintf(self::PRE_AUTH_LINK, $this->getClientId(), $this->getCode(), urlencode($url))
        );
    }
}
