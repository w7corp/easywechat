<?php

namespace Overtrue\Wechat\Services;

use Exception;
use Overtrue\Wechat\Wechat;

class Auth
{
    const API_URL       = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    const API_TOKEN_GET = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const API_USER      = 'https://api.weixin.qq.com/sns/userinfo';

    /**
     * 授权结果
     *
     * {
     *     "access_token":"ACCESS_TOKEN",
     *     "expires_in":7200,
     *     "refresh_token":"REFRESH_TOKEN",
     *     "openid":"OPENID",
     *     "scope":"SCOPE"
     *  }
     *
     * @var array
     */
    protected $authResult;

    /**
     * 已授权用户
     *
     * @var \Overtrue\Wechat\Utils\Bag
     */
    protected $authorizedUser;

    /**
     * 判断是否已经授权
     *
     * @return boolean
     */
    public function authorized()
    {
        if ($this->authResult) {
            return true;
        }

        if (!($code = Wechat::input('code', null))) {
            return false;
        }

        return (bool) $this->authorize($code);
    }

    /**
     * 生成outh URL
     *
     * @param string  $redirect
     * @param string  $state
     * @param string  $scope
     * @param boolean $redirect
     *
     * @return string
     */
    public function url($redirect, $state = '', $scope = 'snsapi_userinfo')
    {
        $params = array(
                   'appid'         => Wechat::getOption('appId'),
                   'scope'         => $scope,
                   'state'         => $state ? : Wechat::getOption('appId'),
                   'redirect_uri'  => $redirect,
                   'response_type' => 'code',
                  );

        return self::API_URL . '?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 直接跳转
     *
     * @param string  $redirect
     * @param string  $state
     * @param string  $scope
     *
     * @return void
     */
    public function redirect($redirect, $state = '', $scope = 'snsapi_userinfo')
    {
        header('Location:' . $this->url($redirect, $state, $scope));
    }

    /**
     * 获取已授权用户
     *
     * @return \Overtrue\Wechat\Utils\Bag
     */
    public function user()
    {
        if ($this->authorizedUser) {
            return $this->authorizedUser;
        }

        if (!$this->authorized()) {
            throw new Exception("未授权");
        }

        if ($this->authResult['scope'] != 'snsapi_userinfo') {
            throw new Exception("OAuth授权类型为snsapi_userinfo时才能使用此接口获取用户信息");
        }

        $queries = array(
                   'access_token' => $this->authResult['access_token'],
                   'openid'       => $this->authResult['openid'],
                   'lang'         => 'zh_CN',
                  );

        return new Bag(Wechat::request('POST', Wechat::self::API_USER, array(), $queries));
    }

    /**
     * 获取access_token
     *
     * @param string $code
     *
     * @return string
     */
    public function getAccessToken()
    {
        $key = 'overtrue.wechat.oauth2.access_token';

        if ($cached = Wechat::service('cache')->get($key)) {
            return $cached;
        }

        Wechat::service('cache')->set($key, $this->authResult['access_token'], $this->authResult['expires_in']);

        return $this->authResult['access_token'];
    }

    /**
     * 通过code授权
     *
     * @param string $code
     *
     * @return string
     */
    protected function authorize($code)
    {
        if ($this->authResult) {
            return $this->authResult;
        }

        // 关闭自动加access_token参数
        Wechat::autoRequestToken(false);

        $params = array(
                   'appid'      => Wechat::getOption('appId'),
                   'secret'     => Wechat::getOption('secret'),
                   'code'       => $code,
                   'grant_type' => 'authorization_code',
                  );

        $authResult = Wechat::request('GET', self::API_TOKEN_GET, $params);

         // 开启自动加access_token参数
        Wechat::autoRequestToken(true);

        //TODO:refresh_token机制
        return $this->authResult = $authResult;
    }
}