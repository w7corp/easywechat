<?php

namespace EasyWeChat\Work\OAuth;

use EasyWeChat\Work\Application;
use Overtrue\Socialite\Providers\WeWork;
use Overtrue\Socialite\SocialiteManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Manager
{
    protected $config;

    /**
     * @var \Overtrue\Socialite\Providers\WeWork
     */
    protected $provider;
    protected $app;

    public function __construct(array $config, Application $app)
    {
        $this->config = $config;
        $this->app = $app;
    }

    public function redirect($redirect = null)
    {
        return new RedirectResponse($this->getProvider()->redirect($redirect));
    }

    public function user()
    {
        $this->getProvider()->withApiAccessToken($this->app['access_token']->getToken()['access_token']);

        return $this->getProvider()->userFromCode($this->app->request->get('code'));
    }

    protected function getProvider(): WeWork
    {
        return $this->provider ?? $this->provider = (new SocialiteManager($this->config))->driver('wework');
    }

    public function __call($name, $arguments)
    {
        return \call_user_func_array([$this->getProvider(), $name], $arguments);
    }
}
