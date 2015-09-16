<?php

/**
 * Client.php.
 *
 * Part of EasyWeChat.
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

namespace EasyWeChat\OAuth;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\Str;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Client.
 */
class Client
{
    /**
     * App id.
     *
     * @var string
     */
    protected $appId;

    /**
     * App secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * Http client.
     *
     * @var Http
     */
    protected $http;

    /**
     * Request object.
     *
     * @var Request
     */
    protected $request;

    const USER_URL = 'https://api.weixin.qq.com/sns/userinfo';
    const ACCESS_TOKEN_URL = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    const TOKEN_REFRESH_URL = 'https://api.weixin.qq.com/sns/oauth2/refresh_token';
    const VALIDATE_URL = 'https://api.weixin.qq.com/sns/auth';
    const AUTHORIZE_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';

    const LAST_SCOPE_SESSION_KEY = 'easywechat.oauth.last_redirect_scope';

    /**
     * Authorize scopes.
     *
     * @var array
     */
    protected $scopes = ['snsapi_base', 'snsapi_userinfo'];

    /**
     * The type of the encoding in the query.
     *
     * @var int Can be either PHP_QUERY_RFC3986 or PHP_QUERY_RFC1738.
     */
    protected $encodingType = PHP_QUERY_RFC1738;

    /**
     * Constructor.
     *
     * @param string  $appId
     * @param string  $secret
     * @param Request $request
     * @param Http    $http
     */
    public function __construct($appId, $secret, Request $request, Http $http)
    {
        $this->appId = $appId;
        $this->secret = $secret;
        $this->request = $request;
        $this->http = $http->setExpectedException(OAuthHttpException::class);
    }

    /**
     * Redirect the user of the application to the WeChat's authentication screen.
     *
     * @param string $to
     * @param string $scope
     *
     * @return RedirectResponse
     *
     * @throws InvalidArgumentException
     */
    public function redirect($to, $scope = 'snsapi_userinfo')
    {
        if (!in_array($scope, $this->scopes)) {
            throw new InvalidArgumentException("Invalid oauth scope:'$scope'");
        }

        $this->request->getSession()->set('state', $state = Str::random(40));
        $this->request->getSession()->set(self::LAST_SCOPE_SESSION_KEY, $scope);

        return new RedirectResponse($this->buildAuthUrlFromBase($to, $scope, $state).'#wechat_redirect');
    }

    /**
     * Silent redirect.
     *
     * @param string $to
     *
     * @return RedirectResponse
     *
     * @throws InvalidArgumentException
     */
    public function silentRedirect($to)
    {
        return $this->redirect($to, 'snsapi_base');
    }

    /**
     * Get authorized User instance.
     *
     * @return User
     *
     * @throws RuntimeException
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new RuntimeException('Invalid state.');
        }

        $accessToken = $this->getAccessToken($this->getCode());

        // snsapi_base
        if ($this->request->getSession()->get(self::LAST_SCOPE_SESSION_KEY) == 'snsapi_base') {
            $user = $this->mapUserToObject(['openid' => $accessToken['openid']]);
        } else { // snsapi_userinfo
            $user = $this->mapUserToObject(
                $this->getUserByAccessToken($accessToken['openid'], $accessToken['access_token'])
            );
        }

        $user->setToken($accessToken['access_token']);
        $user->setRefreshToken($accessToken['refresh_token']);

        return $user;
    }

    /**
     * Get the access token for the given code.
     *
     * @param string $code
     *
     * @return array
     */
    public function getAccessToken($code)
    {
        return $this->http->get(self::ACCESS_TOKEN_URL, $this->getTokenFields($code));
    }

    /**
     * Refresh access_token.
     *
     * @param string $refreshToken
     *
     * @return Collection
     */
    public function refresh($refreshToken)
    {
        $params = $this->getRefreshTokenFields($refreshToken);

        $permission = $this->http->get(self::TOKEN_REFRESH_URL, $params);

        return new Collection($permission);
    }

    /**
     * Get User by access token.
     *
     * @param string $openId
     * @param string $accessToken
     *
     * @return array
     */
    public function getUserByAccessToken($openId, $accessToken)
    {
        $queries = $this->getUserFields($openId, $accessToken);

        return (array)$this->http->get(self::USER_URL, $queries);
    }

    /**
     * Get the GET parameters for the code request.
     *
     * @param string      $redirectUrl
     * @param string      $scope
     * @param string|null $state
     *
     * @return array
     */
    protected function getCodeFields($redirectUrl, $scope, $state = null)
    {
        $fields = [
            'appid' => $this->appId,
            'redirect_uri' => $redirectUrl,
            'scope' => $scope,
            'state' => $state,
            'response_type' => 'code',
        ];

        return $fields;
    }

    /**
     * Get the parameters for the code request.
     *
     * @param string $openId
     * @param string $accessToken
     *
     * @return array
     */
    protected function getUserFields($openId, $accessToken)
    {
        return [
            'openid' => $openId,
            'lang' => 'zh_CN',
            'access_token' => $accessToken,
        ];
    }

    /**
     * Get the authentication URL.
     *
     * @param string $redirectUrl
     * @param string $scope
     * @param string $state
     *
     * @return string
     */
    protected function buildAuthUrlFromBase($redirectUrl, $scope, $state)
    {
        return self::AUTHORIZE_URL.'?'.http_build_query(
            $this->getCodeFields($redirectUrl, $scope, $state),
            '',
            '&',
            $this->encodingType
        );
    }

    /**
     * Determine if the current request / session has a mismatching "state".
     *
     * @return bool
     */
    protected function hasInvalidState()
    {
        $state = $this->request->getSession()->get('state');

        return !(strlen($state) > 0 && $this->request->get('state') === $state);
    }

    /**
     * Get the POST fields for the token request.
     *
     * @param string $code
     *
     * @return array
     */
    protected function getTokenFields($code)
    {
        $params = [
            'appid' => $this->appId,
            'secret' => $this->secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ];

        return $params;
    }

    /**
     * Get the POST fields for the refresh token request.
     *
     * @param string $refreshToken
     *
     * @return array
     */
    protected function getRefreshTokenFields($refreshToken)
    {
        $params = [
            'appid' => $this->appId,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ];

        return $params;
    }

    /**
     * Get the code from the request.
     *
     * @return string
     */
    protected function getCode()
    {
        return $this->request->get('code');
    }

    /**
     * Return User object from array.
     *
     * @param array $user
     *
     * @return User
     */
    protected function mapUserToObject(array $user)
    {
        return new User($user);
    }
}
