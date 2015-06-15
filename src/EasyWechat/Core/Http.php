<?php
/**
 * Http.php
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Core;

use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\Support\Http as HttpClient;
use EasyWeChat\Support\JSON;

/**
 * Class Http
 *
 * @package EasyWeChat\Core
 * @method mixed jsonPost($url, $params = array(), $options = array())
 */
class Http extends HttpClient
{

    /**
     * Access token.
     *
     * @var string
     */
    protected $token;

    /**
     * JSON request flag.
     *
     * @var bool
     */
    protected $json = false;

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
    public function request($url, $method = self::GET, $params = array(), $options = array())
    {
        if ($this->token) {
            $url .= (stripos($url, '?') ? '&' : '?') . 'access_token=' . $this->token;
        }

        $method = strtoupper($method);

        if ($this->json) {
            $options['json'] = true;
        }

        $response = parent::request($url, $method, $params, $options);

        $this->json = false;

        if (empty($response['data'])) {
            throw new HttpException('Empty response.', -1);
        }

        // plain text or JSON
        $textMIME = '~application/json|text/plain~i';

        $contents = JSON::decode($response['data'], true);

        // while the response is an invalid JSON structure, returned the source data
        if (!preg_match($textMIME, $response['content_type'])
            || (JSON_ERROR_NONE !== json_last_error() && false === $contents)
        ) {
            return $response['data'];
        }

        if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }

            throw new FaultException("[{$contents['errcode']}] " . $contents['errcode'], $contents['errcode']);
        }

        if ($contents === array('errcode' => '0', 'errmsg' => 'ok')) {
            return true;
        }

        return $contents;
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (stripos($method, 'json') === 0) {
            $method = strtolower(substr($method, 4));
            $this->json = true;
        }

        $result = call_user_func_array(array($this, $method), $args);

        return $result;
    }
}//end class
