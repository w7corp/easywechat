<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Applications\WeWork\Auth;


use EasyWeChat\Applications\WeWork\Application;
use EasyWeChat\Kernel\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{
    /**
     * {@inheritdoc}.
     */
    protected $prefix = 'easywechat.wework.access_token.';

    /**
     * @var string
     */
    protected $endpointToGetToken = 'gettoken';

    /**
     * @var string
     */
    protected $corpId;

    /**
     * @var string
     */
    protected $secret;

    /**
     * AccessToken constructor.
     *
     * @param \EasyWeChat\Applications\WeWork\Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->corpId = $app['config']->get('corp_id');
        $this->secret = $app['config']->get('secret');
    }

    /**
     * Credential for get token.
     *
     * @return array
     */
    protected function getCredential(): array
    {
        return [
            'corpid' => $this->corpId,
            'corpsecret' => $this->secret,
        ];
    }
}
