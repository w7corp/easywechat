<?php
namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

/**
 * 网页授权
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
     * Cache对象
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Http对象
     *
     * @var Http
     */
    protected $http;

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
     * @var array | boolean
     */
    protected $authResult;

    /**
     * 已授权用户
     *
     * @var \Overtrue\Wechat\Utils\Bag
     */
    protected $authorizedUser;

    const API_USER      = 'https://api.weixin.qq.com/sns/userinfo';
    const API_TOKEN_GET = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const API_URL       = 'https://open.weixin.qq.com/connect/oauth2/authorize';


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
        $this->cache     = new Cache($appId);
        $this->http      = new Http(); // 不需要公用的access_token
    }

    /**
     * 判断是否已经授权
     *
     * @return boolean
     */
    public function authorized()
    {
        if (! empty($this->authResult)) {
            return true;
        }

        $input = new Input();

        if (! $code = $input->get('code')) {
            return false;
        }

        return (bool) $this->authorize($code);
    }

    /**
     * 生成outh URL
     *
     * @param string  $to
     * @param string  $state
     * @param string  $scope
     *
     * @return string
     */
    public function url($to, $scope = 'snsapi_base', $state = 'STATE')
    {
        $params = array(
                   'appid'         => $this->appId,
                   'redirect_uri'  => $to,
                   'response_type' => 'code',
                   'scope'         => $scope,
                   'state'         => $state,
                  );

        return self::API_URL . '?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 直接跳转
     *
     * @param string  $to
     * @param string  $scope
     * @param string  $state
     *
     * @return void
     */
    public function redirect($to, $scope = 'snsapi_base', $state = 'STATE')
    {
        header('Location:' . $this->url($to, $scope, $state));exit;
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

        $url = self::API_USER . '?' . http_build_query($queries);

        return $this->authorizedUser = new Bag($this->http->get($url));
    }

    /**
     * 通过code授权
     *
     * @param string $code
     *
     * @return array | boolean
     */
    protected function authorize($code)
    {
        if (! empty($this->authResult)) {
            return $this->authResult;
        }

        $params = array(
                   'appid'      => $this->appId,
                   'secret'     => $this->appSecret,
                   'code'       => $code,
                   'grant_type' => 'authorization_code',
                  );

        $authResult = $this->http->get(self::API_TOKEN_GET, $params);

        //TODO:refresh_token机制
        return $this->authResult = $authResult;
    }

    /**
     * 获取access_token
     *
     * 注意：这个是OAuth2用的access_token，与普通access_token不一样
     *
     * @return string
     */
    public function getAccessToken()
    {
        $key = 'overtrue.wechat.oauth2.access_token';

        return $this->cache->get($key, function($key) {

            $this->cache->set($key, $this->authResult['access_token'], $this->authResult['expires_in']);

            return $this->authResult['access_token'];
        });
    }
}
