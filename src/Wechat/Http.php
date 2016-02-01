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
 * Http.php.
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

use Overtrue\Wechat\Utils\Http as HttpClient;
use Overtrue\Wechat\Utils\JSON;

/**
 * @method mixed jsonPost($url, $params = array(), $options = array())
 */
class Http extends HttpClient
{
    /**
     * token.
     *
     * @var AccessToken
     */
    protected $token;

    /**
     * json请求
     *
     * @var bool
     */
    protected $json = false;

    /**
     * 缓存类.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * constructor.
     *
     * @param AccessToken $token
     */
    public function __construct(AccessToken $token = null)
    {
        $this->token = $token;
        parent::__construct();
    }

    /**
     * 设置请求access_token.
     *
     * @param AccessToken $token
     */
    public function setToken(AccessToken $token)
    {
        $this->token = $token;
    }

    /**
     * 发起一个HTTP/HTTPS的请求
     *
     * @param string $url     接口的URL
     * @param string $method  请求类型   GET | POST
     * @param array  $params  接口参数
     * @param array  $options 其它选项
     * @param int    $retry   重试次数
     *
     * @return array | boolean
     */
    public function request($url, $method = self::GET, $params = array(), $options = array(), $retry = 1)
    {
        if ($this->token) {
            // clear repeat token
            $url = preg_replace('/[\?&]access_token=.*/i', '', $url);
            $url .= (stripos($url, '?') ? '&' : '?').'access_token='.$this->token->getToken();
        }

        $method = strtoupper($method);

        if ($this->json) {
            $options['json'] = true;
        }

        $response = parent::request($url, $method, $params, $options);

        $this->json = false;

        if (empty($response['data'])) {
            throw new Exception('服务器无响应');
        }

        if (!preg_match('/^[\[\{]\"/', $response['data'])) {
            return $response['data'];
        }

        $contents = json_decode($this->fuckTheWeChatInvalidJSON($response['data']), true);

        // while the response is an invalid JSON structure, returned the source data
        if (JSON_ERROR_NONE !== json_last_error()) {
            return $response['data'];
        }

        if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }

            // access token 超时重试处理
            if ($this->token && in_array($contents['errcode'], array('40001', '42001')) && $retry > 0) {
                // force refresh
                $this->token->getToken(true);

                return $this->request($url, $method, $params, $options, --$retry);
            }

            throw new Exception("[{$contents['errcode']}] ".$contents['errmsg'], $contents['errcode']);
        }

        if (is_array($contents) && array_keys($contents) == array("errcode", "errmsg")) {
            return true;
        }

        return $contents;
    }

    /**
     * Filter the invalid JSON string.
     *
     * @param string $invalidJSON
     *
     * @return string
     */
    protected function fuckTheWeChatInvalidJSON($invalidJSON)
    {
        return preg_replace("/\p{Cc}/u", '', trim($invalidJSON));
    }

    /**
     * 魔术调用.
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
}
