<?php

/**
 * Http.php.
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

namespace EasyWeChat\Core;

use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Support\Http as HttpClient;

/**
 * Class Http.
 *
 * @method mixed json($url, $params = array(), $options = array())
 */
class Http extends HttpClient
{
    const WECHAT_REPONSE_ERROR_NONE = 0;

    /**
     * Access token.
     *
     * @var string
     */
    protected $token;

    /**
     * Defualt exception.
     *
     * @var string
     */
    protected $exception = 'EasyWeChat\Core\Exceptions\HttpException';

    /**
     * Constructor.
     *
     * @param AccessToken|null $token
     */
    public function __construct(AccessToken $token = null)
    {
        $this->token = $token;

        parent::__construct();
    }

    /**
     * Set token.
     *
     * @param AccessToken $token
     */
    public function setToken(AccessToken $token)
    {
        $this->token = $token;
    }

    /**
     * Return token.
     *
     * @return AccessToken|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set exception to be throw when an error occurs.
     *
     * @param Exception $exception
     *
     * @return Http
     *
     * @throws InvalidArgumentException
     */
    public function setExpectedException($exception)
    {
        if (!is_subclass_of($exception, 'Exception')) {
            throw new InvalidArgumentException('Invalid Exception name.');
        }

        $this->exception = is_string($exception) ? $exception : get_class($exception);

        return $this;
    }

    /**
     * Return expected exception name.
     *
     * @return string
     */
    public function getExpectedException()
    {
        return $this->exception;
    }

    /**
     * Make a request.
     *
     * @param string $url
     * @param string $method
     * @param array  $params
     * @param array  $options
     *
     * @return array|bool
     *
     * @throws FaultException
     * @throws HttpException
     */
    public function request($url, $method = self::GET, $params = [], $options = [])
    {
        if ($this->token) {
            $url .= (stripos($url, '?') ? '&' : '?').'access_token='.$this->token;
        }

        $method = strtoupper($method);

        $response = parent::request($url, $method, $params, $options);

        $this->json = false;

        if (empty($response['data'])) {
            throw new HttpException('Empty response.', -1);
        }

        $contents = json_decode($response['data'], true);

        // while the response is an invalid JSON structure, returned the source data
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $response['data'];
        }

        if (isset($contents['errcode']) && 0 != $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }

            $this->thorwException($contents['errmsg'], $contents['errcode']);
        }

        if (isset($contents['errcode']) && $contents['errcode'] == self::WECHAT_REPONSE_ERROR_NONE) {
            return true;
        }

        return $contents;
    }

    /**
     * Throw Http Exception.
     *
     * @param string $msg
     * @param int    $code
     */
    protected function thorwException($msg, $code)
    {
        throw new $this->exception($msg, $code);
    }
}
