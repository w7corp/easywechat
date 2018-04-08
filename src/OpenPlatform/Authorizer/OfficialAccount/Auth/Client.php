<?php
/**
 * Created by PhpStorm.
 * User: keal
 * Date: 2018/4/3
 * Time: 下午2:53
 */

namespace EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Auth;


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
     * Client constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer  $app
     * @param \EasyWeChat\OpenPlatform\Application $component
     */
    public function __construct(ServiceContainer $app, Application $component)
    {
        parent::__construct($app);

        $this->component = $component;
    }

    /**
     * 从第三方平台跳转至微信公众平台授权注册页面, 授权注册小程序.
     *
     * @param string $callbackUrl
     * @param bool $copyWxVerify
     *
     * @return string
     */
    public function getFastRegistrationUrl(string $callbackUrl, bool $copyWxVerify = true) : string
    {
        $queries = [
            'copy_wx_verify' => $copyWxVerify ? 1 : 0,
            'component_appid' => $this->component['config']['app_id'],
            'appid' => $this->app['config']['app_id'],
            'redirect_uri' => $callbackUrl,
        ];

        return 'https://mp.weixin.qq.com/cgi-bin/fastregisterauth?'.http_build_query($queries);
    }
}