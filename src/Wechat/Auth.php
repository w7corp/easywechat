<?php

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

/**
 * OAuth 网页授权获取用户信息
 */
class Auth
{
    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

    /**
     * 输入
     *
     * @var Bag
     */
    protected $input;

    /**
     * 已授权用户
     *
     * @var \Overtrue\Wechat\Utils\Bag
     */
    protected $authorizedUser;

    const API_USER          = 'https://api.weixin.qq.com/sns/userinfo';
    const API_TOKEN_GET     = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const API_TOKEN_REFRESH = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    const API_URL           = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->http      = new Http(); // 不需要公用的access_token
        $this->input     = new Input();
    }

    /**
     * 生成outh URL
     *
     * @param string $to
     * @param string $state
     * @param string $scope
     *
     * @return string
     */
    public function url($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        $to !== null || $to = Url::current();

        $params = array(
                   'appid'         => $this->appId,
                   'redirect_uri'  => $to,
                   'response_type' => 'code',
                   'scope'         => $scope,
                   'state'         => $state,
                  );

        return self::API_URL.'?'.http_build_query($params).'#wechat_redirect';
    }

    /**
     * 直接跳转
     *
     * @param string $to
     * @param string $scope
     * @param string $state
     */
    public function redirect($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        $to !== null || $to = Url::current();

        header('Location:'.$this->url($to, $scope, $state));

        exit;
    }

    /**
     * 获取已授权用户
     *
     * @return \Overtrue\Wechat\Utils\Bag | null
     */
    public function user()
    {
        if ($this->authorizedUser) {
            return $this->authorizedUser;
        }

        if (!$this->input->has('state')) {
            return;
        }

        if ((!$code = $this->input->get('code')) && $this->input->has('state')) {
            return;
        }

        $permission = $this->getAccessPermission($code);

        if ($permission['scope'] !== 'snsapi_userinfo') {
            $user = new Bag(array('openid' => $permission['openid']));
        } else {
            $user = $this->getUser($permission['openid'], $permission['access_token']);
        }

        return $this->authorizedUser = $user;
    }

    /**
     * 通过授权获取用户
     *
     * @param string $to
     * @param string $state
     * @param string $scope
     *
     * @return Bag | null
     */
    public function authorize($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        if (!$this->input->has('state') && !$this->input->has('code')) {
            $this->redirect($to, $scope, $state);
        }

        return $this->user();
    }

    /**
     * 获取access token
     *
     * @param string $code
     *
     * @return string
     */
    public function getAccessPermission($code)
    {
        $params = array(
               'appid'      => $this->appId,
               'secret'     => $this->appSecret,
               'code'       => $code,
               'grant_type' => 'authorization_code',
              );

        return $this->http->get(self::API_TOKEN_GET, $params);
    }

    /**
     * 获取用户信息
     *
     * @param string $openId
     * @param string $accessToken
     *
     * @return array
     */
    protected function getUser($openId, $accessToken)
    {
        $queries = array(
                   'access_token' => $accessToken,
                   'openid'       => $openId,
                   'lang'         => 'zh_CN',
                  );

        $url = self::API_USER.'?'.http_build_query($queries);

        return new Bag($this->http->get($url));
    }
}
