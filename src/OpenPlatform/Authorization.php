<?php
/**
 * Authorization.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    lixiao <leonlx126@gmail.com>
 * @copyright 2016
 *
 * @see      https://github.com/overtrue
 * @see      http://overtrue.me
 */

namespace EasyWeChat\OpenPlatform;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\Exception;
use EasyWeChat\OpenPlatform\Components\Authorizer;
use EasyWeChat\OpenPlatform\Traits\Caches;
use EasyWeChat\Support\Collection;

class Authorization
{
    use Caches;

    const CACHE_KEY_ACCESS_TOKEN = 'easywechat.open_platform.authorizer_access_token';
    const CACHE_KEY_REFRESH_TOKEN = 'easywechat.open_platform.authorizer_refresh_token';

    /**
     * Authorizer API.
     *
     * @var Authorizer
     */
    private $authorizer;

    /**
     * Open Platform App Id, aka, Component App Id.
     *
     * @var string
     */
    private $appId;

    /**
     * Authorizer App Id.
     *
     * @var string
     */
    private $authorizerAppId;

    /**
     * Auth code.
     *
     * @var string
     */
    private $authCode;

    public function __construct(Authorizer $authorizer, $appId,
                                Cache $cache = null)
    {
        $this->authorizer = $authorizer;
        $this->appId = $appId;
        $this->setCache($cache);
    }

    /**
     * Sets the authorizer app id.
     *
     * @param string $authorizerAppId
     */
    public function setAuthorizerAppId($authorizerAppId)
    {
        $this->authorizerAppId = $authorizerAppId;
    }

    /**
     * Gets the authorizer app id, or throws if not found.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getAuthorizerAppId()
    {
        if (!$this->authorizerAppId) {
            throw new Exception(
                'Authorizer App Id is not present, you may not make the authorization yet.'
            );
        }

        return $this->authorizerAppId;
    }

    /**
     * Sets the auth code.
     *
     * @param $code
     */
    public function setAuthCode($code)
    {
        $this->authCode = $code;
    }

    /**
     * Gets the auth code.
     *
     * @return string
     */
    public function getAuthCode()
    {
        return $this->authCode;
    }

    /**
     * Sets the auth info from the message of the auth event sent by WeChat.
     *
     * @param Collection $message
     */
    public function setFromAuthMessage(Collection $message)
    {
        if ($message->has('AuthorizerAppid')) {
            $this->setAuthorizerAppId($message->get('AuthorizerAppid'));
        }
        if ($message->has('AuthorizationCode')) {
            $this->setAuthCode($message->get('AuthorizationCode'));
        }
    }

    /**
     * Handles authorization: calls the API, saves the tokens.
     *
     * @return Collection
     */
    public function handleAuthorization()
    {
        $info = $this->getAuthorizationInfo();

        $appId = $info['authorization_info']['authorizer_appid'];
        $this->setAuthorizerAppId($appId);

        $this->saveAuthorizerAccessToken($info['authorization_info']);
        $this->saveAuthorizerRefreshToken($info['authorization_info']);

        $authorizerInfo = $this->getAuthorizerInfo();
        // Duplicated info.
        $authorizerInfo->forget('authorization_info');
        $info->merge($authorizerInfo->all());

        return $info;
    }

    /**
     * Handles the authorizer access token: calls the API, saves the token.
     *
     * @return string the authorizer access token
     */
    public function handleAuthorizerAccessToken()
    {
        $data = $this->authorizer->getAuthorizationToken(
            $this->getAuthorizerAppId(),
            $this->getAuthorizerRefreshToken()
        );

        $this->saveAuthorizerAccessToken($data);

        return $data['authorizer_access_token'];
    }

    /**
     * Gets the authorization information.
     * Like authorizer app id, access token, refresh token, function scope, etc.
     *
     * @return Collection
     */
    public function getAuthorizationInfo()
    {
        $result = $this->authorizer->getAuthorizationInfo($this->getAuthCode());
        if (is_array($result)) {
            $result = new Collection($result);
        }

        return $result;
    }

    /**
     * Gets the authorizer information.
     * Like authorizer name, logo, business, etc.
     *
     * @return Collection
     */
    public function getAuthorizerInfo()
    {
        $result = $this->authorizer->getAuthorizerInfo($this->getAuthorizerAppId());
        if (is_array($result)) {
            $result = new Collection($result);
        }

        return $result;
    }

    /**
     * Saves the authorizer access token in cache.
     *
     * @param Collection|array $data array structure from WeChat API result
     */
    public function saveAuthorizerAccessToken($data)
    {
        $accessToken = $data['authorizer_access_token'];
        // Expiration time, -100 to avoid any delay.
        $expire = $data['expires_in'] - 100;

        $this->set($this->getAuthorizerAccessTokenKey(), $accessToken, $expire);
    }

    /**
     * Gets the authorizer access token.
     *
     * @return string
     */
    public function getAuthorizerAccessToken()
    {
        return $this->get($this->getAuthorizerAccessTokenKey());
    }

    /**
     * Saves the authorizer refresh token in cache.
     *
     * @param Collection|array $data array structure from WeChat API result
     */
    public function saveAuthorizerRefreshToken($data)
    {
        $refreshToken = $data['authorizer_refresh_token'];

        $this->set($this->getAuthorizerRefreshTokenKey(), $refreshToken);
    }

    /**
     * Gets the authorizer refresh token.
     *
     * @return string
     *
     * @throws Exception when refresh token is not present
     */
    public function getAuthorizerRefreshToken()
    {
        if ($token = $this->get($this->getAuthorizerRefreshTokenKey())) {
            return $token;
        }

        throw new Exception(
            'Authorizer Refresh Token is not present, you may not make the authorization yet.'
        );
    }

    /**
     * Removes the authorizer access token from cache.
     */
    public function removeAuthorizerAccessToken()
    {
        $this->remove($this->getAuthorizerAccessTokenKey());
    }

    /**
     * Removes the authorizer refresh token from cache.
     */
    public function removeAuthorizerRefreshToken()
    {
        $this->remove($this->getAuthorizerRefreshTokenKey());
    }

    /**
     * Gets the authorizer access token cache key.
     *
     * @return string
     */
    public function getAuthorizerAccessTokenKey()
    {
        return self::CACHE_KEY_ACCESS_TOKEN
            .'.'.$this->appId
            .'.'.$this->getAuthorizerAppId();
    }

    /**
     * Gets the authorizer refresh token cache key.
     *
     * @return string
     */
    public function getAuthorizerRefreshTokenKey()
    {
        return self::CACHE_KEY_REFRESH_TOKEN
            .'.'.$this->appId
            .'.'.$this->getAuthorizerAppId();
    }
}
