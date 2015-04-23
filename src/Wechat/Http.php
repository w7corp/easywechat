<?php
namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Http as HttpClient;

class Http extends HttpClient
{
    /**
     * token
     *
     * @var token
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
     * 生成url
     *
     * @param string $url     基础网址
     * @param array  $queries 查询
     *
     * @return string
     */
    public function makeUrl($url, $queries = array())
    {
        !$this->token || $queries['access_token'] = $this->token;

        return $url . (empty($queries) ? '' : ('?' . http_build_query($queries)));
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
     * @return array|boolean
     */
    public function request($url, $method = self::GET, $params = array(), $options = array())
    {
        if ($this->token) {
            $url .= (stripos($url, '?') ? '&' : '?') .'access_token=' . $this->token;
        }

        $method = strtolower($method);

        if ($this->json) {
            $options['json'] = true;
        }

        $response = parent::{$method}($url, $params, $options);

        if (empty($response['data'])) {
            throw new Exception("服务器无响应");
        }

        $contents = json_decode($response['data'], true);

        if(isset($contents['errcode'])) {
            if ($contents['errmsg'] == 'ok') {
                return true;
            }

            throw new Exception("[{$contents['errcode']}] ".$contents['errmsg'], $contents['errcode']);
        }

        $this->json = false;

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

        $result = call_user_func_array($this->$method, $args);

        return $result;
    }
}