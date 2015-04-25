<?php
namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Http as HttpClient;

/**
 * @method mixed jsonPost($url, $params, $options)
 */
class Http extends HttpClient
{
    /**
     * token
     *
     * @var string
     */
    protected $token;

    /**
     * json请求
     *
     * @var boolean
     */
    protected $json = false;

    /**
     * 缓存类
     *
     * @var Cache
     */
    protected $cache;


    /**
     * constructor
     *
     * @param string $token
     */
    public function __construct($token = null)
    {
        $this->token = $token;
        parent::__construct();
    }

    /**
     * 设置请求access_token
     *
     * @param string $token
     *
     * @return void
     */
    public function setToken($token)
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
     *
     * @return array | boolean
     */
    public function request($url, $method = self::GET, $params = array(), $options = array())
    {
        if ($this->token) {
            $url .= (stripos($url, '?') ? '&' : '?') .'access_token=' . $this->token;
        }

        $method = strtoupper($method);

        if ($this->json) {
            $options['json'] = true;
        }

        $response = parent::request($url, $method, $params, $options);

        $this->json = false;

        if (empty($response['data'])) {
            throw new Exception("服务器无响应");
        }

        $contents = json_decode($response['data'], true);

        if(isset($contents['errcode'])) {
            if ($contents['errmsg'] == 'ok') {
                return true;
            }

            if (empty($contents['errmsg'])) {
                $contents['errmsg'] = 'Unknown';
            }

            throw new Exception("[{$contents['errcode']}] ". $contents['errcode'], $contents['errcode']);
        }


        return $contents;
    }

    /**
     * 魔术调用
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