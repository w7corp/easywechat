<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Auth.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;

/**
 * OAuth 网页授权获取用户信息.
 */
class Auth
{
    /**
     * 应用ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret.
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
     * 输入.
     *
     * @var Bag
     */
    protected $input;

    /**
     * 获取上一次的授权信息.
     *
     * @var array
     */
    protected $lastPermission;

    /**
     * 已授权用户.
     *
     * @var \Overtrue\Wechat\Utils\Bag
     */
    protected $authorizedUser;

    const API_USER = 'https://api.weixin.qq.com/sns/userinfo';
    const API_TOKEN_GET = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const API_TOKEN_REFRESH = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    const API_TOKEN_VALIDATE = 'https://api.weixin.qq.com/sns/auth';
    const API_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->http = new Http(); // 不需要公用的access_token
        $this->input = new Input();
    }

    /**
     * 生成outh URL.
     *
     * @param string $to
     * @param string $scope
     * @param string $state
     *
     * @return string
     */
    public function url($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        $to !== null || $to = Url::current();

        $params = array(
                   'appid' => $this->appId,
                   'redirect_uri' => $to,
                   'response_type' => 'code',
                   'scope' => $scope,
                   'state' => $state,
                  );

        return self::API_URL.'?'.http_build_query($params).'#wechat_redirect';
    }

    /**
     * 直接跳转.
     *
     * @param string $to
     * @param string $scope
     * @param string $state
     */
    public function redirect($to = null, $scope = 'snsapi_userinfo', $state = null)
    {
        $state = $state ? $state : md5(time());
        header('Location:'.$this->url($to, $scope, $state));

        exit;
    }

    /**
     * 获取已授权用户.
     *
     * @return \Overtrue\Wechat\Utils\Bag | null
     */
    public function user()
    {
        if ($this->authorizedUser
            || !$this->input->get('state')
            || (!$code = $this->input->get('code')) && $this->input->get('state')) {
            return $this->authorizedUser;
        }

        $permission = $this->getAccessPermission($code);

        if ($permission['scope'] !== 'snsapi_userinfo') {
            $user = new Bag($permission);
        } else {
            $user = $this->getUser($permission['openid'], $permission['access_token']);
        }

        return $this->authorizedUser = $user;
    }

    /**
     * 通过授权获取用户.
     *
     * @param string $to
     * @param string $state
     * @param string $scope
     *
     * @return Bag | null
     */
    public function authorize($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        if (!$this->input->get('state') && !$this->input->get('code')) {
            $this->redirect($to, $scope, $state);
        }

        return $this->user();
    }

    /**
     * 检查 Access Token 是否有效.
     *
     * @param string $accessToken
     * @param string $openId
     *
     * @return bool
     */
    public function accessTokenIsValid($accessToken, $openId)
    {
        $params = array(
                   'openid' => $openId,
                   'access_token' => $accessToken,
                  );
        try {
            $this->http->get(self::API_TOKEN_VALIDATE, $params);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 刷新 access_token.
     *
     * @param string $refreshToken
     *
     * @return Bag
     */
    public function refresh($refreshToken)
    {
        $params = array(
                   'appid' => $this->appId,
                   'grant_type' => 'refresh_token',
                   'refresh_token' => $refreshToken,
                  );

        $permission = $this->http->get(self::API_TOKEN_REFRESH, $params);

        $this->lastPermission = array_merge($this->lastPermission, $permission);

        return new Bag($permission);
    }

    /**
     * 获取用户信息.
     *
     * @param string $openId
     * @param string $accessToken
     *
     * @return array
     */
    public function getUser($openId, $accessToken)
    {
        $queries = array(
                    'access_token' => $accessToken,
                    'openid' => $openId,
                    'lang' => 'zh_CN',
                   );

        $url = self::API_USER.'?'.http_build_query($queries);

        return new Bag($this->http->get($url));
    }

    /**
     * 获取access token.
     *
     * @param string $code
     *
     * @return string
     */
    public function getAccessPermission($code)
    {
        $params = array(
                   'appid' => $this->appId,
                   'secret' => $this->appSecret,
                   'code' => $code,
                   'grant_type' => 'authorization_code',
                  );

        return $this->lastPermission = $this->http->get(self::API_TOKEN_GET, $params);
    }

    /**
     * 魔术访问.
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (isset($this->lastPermission[$property])) {
            return $this->lastPermission[$property];
        }
    }
}
