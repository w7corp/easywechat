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

use EasyWeChat\Support\HasHttpRequests;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client
{
    use HasHttpRequests;

    /**
     * @var string
     */
    protected $componentClientId;

    /**
     * Client Constructor.
     *
     * @param string $clientId
     */
    public function __construct(string $clientId)
    {
        $this->componentClientId = $clientId;
    }

    /**
     * Create PreAuthorization code.
     *
     * @return mixed
     */
    public function createCode()
    {
        $params = [
            'component_appid' => $this->componentClientId,
        ];

        return $this->parseJSON(
            $this->postJson('https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode', $params)
        );
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
        $result = $this->createCode();
        $url = sprintf(
            'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s',
            $this->clientId, $result['auth_code'], urlencode($to)
        );

        return new RedirectResponse($url);
    }
}
