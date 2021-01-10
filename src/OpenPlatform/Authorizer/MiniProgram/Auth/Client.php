<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Auth;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Application;

class Client extends BaseClient
{
    /**
     * @var \EasyWeChat\OpenPlatform\Application
     */
    protected $component;

    /**
     * @param \EasyWeChat\Kernel\ServiceContainer  $app
     * @param \EasyWeChat\OpenPlatform\Application $component
     */
    public function __construct(ServiceContainer $app, Application $component)
    {
        parent::__construct($app);

        $this->component = $component;
    }

    /**
     * Get session info by code.
     *
     * @param string $code
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function session(string $code)
    {
        $params = [
            'appid' => $this->app['config']['app_id'],
            'js_code' => $code,
            'grant_type' => 'authorization_code',
            'component_appid' => $this->component['config']['app_id'],
            'component_access_token' => $this->component['access_token']->getToken()['component_access_token'],
        ];

        return $this->httpGet('sns/component/jscode2session', $params);
    }
}
