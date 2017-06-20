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

use EasyWeChat\Kernel\BaseClient;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Create PreAuthorization code.
     *
     * @return mixed
     */
    public function createCode($returnRaw = false)
    {
        $params = [
            'component_appid' => $this->app['config']['app_id'],
        ];

        return $this->request('api_create_preauthcode', 'POST', ['json' => $params], $returnRaw);
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
        $result = json_decode($this->createCode(true)->getBody(), true);

        $url = sprintf(
            'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
            $this->app['config']['app_id'], $result['pre_auth_code'], urlencode($to)
        );

        return new RedirectResponse($url);
    }
}
