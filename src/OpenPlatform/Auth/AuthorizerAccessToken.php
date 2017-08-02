<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OpenPlatform\Auth;

use EasyWeChat\Kernel\AccessToken as BaseAccessToken;
use EasyWeChat\OpenPlatform\Application;
use Pimple\Container;

/**
 * Class AuthorizerAccessToken.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class AuthorizerAccessToken extends BaseAccessToken
{
    /**
     * @var string
     */
    protected $requestMethod = 'POST';

    /**
     * @var string
     */
    protected $queryName = 'access_token';

    /**
     * {@inheritdoc}.
     */
    protected $tokenKey = 'authorizer_access_token';

    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    protected $openPlatform;

    /**
     * AuthorizerAccessToken constructor.
     *
     * @param \Pimple\Container                    $app
     * @param \EasyWeChat\OpenPlatform\Application $openPlatform
     */
    public function __construct(Container $app, Application $openPlatform)
    {
        parent::__construct($app);
        $this->openPlatform = $openPlatform;
    }

    /**
     * {@inheritdoc}.
     */
    protected function getCredentials(): array
    {
        return [
            'component_appid' => $this->openPlatform['config']['app_id'],
            'authorizer_appid' => $this->app['config']['app_id'],
            'authorizer_refresh_token' => $this->app['config']['refresh_token'],
        ];
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return 'cgi-bin/component/api_authorizer_token?'.http_build_query([
            'component_access_token' => $this->openPlatform->access_token->getToken()['component_access_token'],
        ]);
    }
}
