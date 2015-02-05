<?php

namespace Overtrue\Wechat;

use Exception;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\Http;
use Overtrue\Wechat\Utils\Crypt;
use Overtrue\Wechat\Messages\AbstractMessage;

class Wechat
{
    /**
     * POST输入
     *
     * @var Overtrue\Wechat\Utils\Bag
     */
    protected $post;

    /**
     * GET输入
     *
     * @var Overtrue\Wechat\Utils\Bag
     */
    protected $query;

    /**
     * 选项
     *
     * @var Overtrue\Wechat\Utils\Bag
     */
    protected $options;

    /**
     * 监听器
     *
     * @var Overtrue\Wechat\Utils\Bag
     */
    protected $listeners;

    /**
     * 是否为加密模式
     *
     * @var boolean
     */
    protected $security = false;

    /**
     * 错误处理器
     *
     * @var callable
     */
    protected $errorHandler;

    /**
     * 缓存写入器
     *
     * @var callable
     */
    protected $cacheWriter;

    /**
     * 缓存读取器
     *
     * @var callable
     */
    protected $cacheReader;

    /**
     * access_token
     *
     * @var string
     */
    protected $accessToken;

    /**
     * 自动添加access_token
     *
     * @var boolean
     */
    static protected $autoRequestToken = true;

    /**
     * Wechat实例
     *
     * @var \Overtrue\Wechat\Wechat
     */
    protected static $instance = null;

    /**
     * 服务
     *
     * @var array
     */
    protected $services = array(
                            'auth',
                            'user',
                            'group',
                            'staff',
                            'menu',
                            'ticket',
                          );

    /**
     * 已经实例化过的服务
     *
     * @var array
     */
    protected $resolved = array();

    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';


    /**
     * 获取实例
     *
     * @param array $options
     */
    private function __construct($options)
    {
        if (empty($options['app_id'])
            || empty($options['secret'])
            || empty($options['token'])) {
            throw new Exception("配置至少包含三项'app_id'、'secret'、'token'且不能为空！");
        }

        $this->listeners = new Bag;
        $this->options   = new Bag($options);
        $this->query     = new Bag($_REQUEST);
        $this->post      = new Bag($this->getPost());

        set_exception_handler(function($e){
            if ($this->errorHandler) {
                return call_user_func_array($this->errorHandler, array($e));
            }

            throw $e;
        });
    }

    private function __clone() {}

    /**
     * 创建实例
     *
     * @param array $options
     *
     * @return \Overtrue\Wechat\Wechat
     */
    static public function make($options)
    {
        !is_null(self::$instance) || self::$instance = new static($options);

        return self::$instance;
    }

    /**
     * 监听
     *
     * @param string   $target
     * @param string   $type
     * @param callable $callback
     *
     * @return string
     */
    public function on($target, $type, $callback = null)
    {
        if (is_callable($type)) {
            $callback = $type;
            $type     = '*';
        }

        $this->{$target}($type, $callback);
    }

    /**
     * 监听事件
     *
     * @param string   $type
     * @param callable $function
     *
     * @return mixed
     */
    public function event($type, $function)
    {
        $this->listeners->add("event.{$type}", $function);
    }

    /**
     * 监听消息
     *
     * @param string   $type
     * @param callable $function
     *
     * @return string
     */
    public function message($type, $function)
    {
        $this->listeners->add("message.{$type}", $function);
    }

    /**
     * handle服务端并返回字符串内容
     *
     * @return mixed
     */
    public function serve()
    {
        if (!$this->checkSignature()) {
            throw new Exception("Bad Request", 400);
        }

        if ($this->query->has('echostr')) {
            return $this->query->echostr;
        }

        $response = $this->handleRequest();

        return $this->response($response);
    }

    /**
     * 错误处理器
     *
     * @param callback $handler
     *
     * @return void
     */
    public function error($handler)
    {
        is_callable($handler) && $this->errorHandler = $handler;
    }

    /**
     * 获取服务
     *
     * @param string $service
     *
     * @return mixed
     */
    public function get($service)
    {
        if (!in_array($service, $this->services)) {
            throw new Exception("未知的服务'{$serve}'");
        }

        if (isset($this->resolved[$service])) {
            return $this->resolved[$service];
        }

        $service = "Overtrue\Wechat\Services\\" . ucfirst($service);

        return $this->resolved[$service] = new $service($this);
    }

    /**
     * 设置缓存写入器
     *
     * @param callable $handler
     *
     * @return void
     */
    public function cacheWriter($handler)
    {
        is_callable($handler) && $this->cacheWriter = $handler;
    }

    /**
     * 设置缓存读取器
     *
     * @param callable $handler
     *
     * @return void
     */
    public function cacheReader($handler)
    {
        is_callable($handler) && $this->cacheReader = $handler;
    }

    /**
     * 自动添加access_token参数
     *
     * @param boolean $status
     *
     * @return void
     */
    public function autoRequestToken($status)
    {
        $this->autoRequestToken = (bool) $status;
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
        if ($this->autoRequestToken) {
            $queries['access_token'] = $this->getAccessToken();
        }

        return $url . (empty($queries) ? '' : ('?' . http_build_query($queries)));
    }

    /**
     * 默认的缓存写入器
     *
     * @param string  $key
     * @param mixed   $value
     * @param integer $lifetime
     *
     * @return void
     */
    protected function fileCacheWriter($key, $value, $lifetime = 7200)
    {
        $data = array(
                 'token'      => $value,
                 'expired_at' => time() + $lifetime - 2, //XXX: 减去2秒更可靠的说
                );

        if (!file_put_contents($this->getCacheFile($key), serialize($data))) {
            throw new Exception("Access toekn 缓存失败");
        }
    }

    /**
     * 默认的缓存读取器
     *
     * @param string   $key
     *
     * @return void
     */
    protected function fileCacheReader($key)
    {
        $file = $this->getCacheFile($key);

        if (file_exists($file) && $token = unserialize(file_get_contents($file))) {
            return $token['expired_at'] > time() ? $token['token'] : null;
        }

        return null;
    }

    /**
     * 获取缓存文件名
     *
     * @param string $key
     *
     * @return string
     */
    protected function getCacheFile($key)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($this->options->app_id . $key);
    }

    /**
     * 写入/读取缓存
     *
     * @param string  $key
     * @param mixed   $value
     * @param integer $lifetime
     *
     * @return mixed
     */
    protected function cache($key, $value = null, $lifetime = 7200)
    {
        if ($value) {
            $handler = $this->cacheWriter ? : array($this, 'fileCacheWriter');
        } else {
            $handler = $this->cacheReader ? : array($this, 'fileCacheReader');
        }

        return call_user_func_array($handler, array($key, $value, $lifetime));
    }

    /**
     * 获取access_token
     *
     * @return string
     */
    protected function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $key = 'overtrue.wechat.access_token';

        if ($cached = $this->cache($key)) {
            return $cached;
        }

        // 关闭自动加access_token参数
        $this->autoRequestToken(false);

        $url = $this->makeUrl(self::API_TOKEN_GET, array(
                                                    'appid'      => $this->options->app_id,
                                                    'secret'     => $this->options->secret,
                                                    'grant_type' => 'client_credential',
                                                   ));
        // 开启自动加access_token参数
        $this->autoRequestToken(true);

        $token = $this->request('GET', $url);

        $this->cache($key, $token['access_token'], $token['expires_in']);

        return $token['access_token'];
    }

    /**
     * 发起一个HTTP/HTTPS的请求
     *
     * @param string $method 请求类型   GET | POST
     * @param string $url    接口的URL
     * @param array  $params 接口参数
     * @param array  $files  图片信息
     *
     * @return array
     */
    public function request($method, $url, array $params = array(), array $files = array())
    {
        $response = Http::request($method, $url, $params, array(), $files);

        if (empty($response)) {
            throw new Exception("服务器无响应");
        }

        $contents = json_decode($response, true);

        if(!empty($contents['errcode'])){
            throw new Exception("[{$contents['errcode']}] ".$contents['errmsg'], $contents['errcode']);
        }

        return $contents;
    }

    /**
     * 生成回复内容
     *
     * @param mixed $response
     *
     * @return string
     */
    protected function response($response)
    {
        if (is_string($response)) {
            //TODO：修改消息生成方式
            $response = Message::make(Message::TEXT)->with('content', $response);
        }

        if ($response instanceof AbstractMessage) {
            $response->from($this->post->ToUserName)->to($this->post->FromUserName);

            $xml = $response->formatToServer();

            if ($this->security) {
                return $this->getCryptor()->encryptMsg($xml, $this->query->nonce, $this->query->timestamp);
            }

            return $xml;
        }

        return null;
    }

    /**
     * 检查微信签名有效性
     */
    protected function checkSignature()
    {
        $input = array(
                $this->options->token,
                $this->query->timestamp,
                $this->query->nonce,
              );

        sort($input, SORT_STRING);

        return sha1(implode($input)) === $this->query->signature;
    }

    /**
     * 获取POST请求数据
     *
     * @return array
     */
    protected function getPost()
    {
        $xmlInput = !empty($GLOBALS["HTTP_RAW_POST_DATA"])
                ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");

        $input = XML::parse($xmlInput);

        if ($this->query->encrypt_type == 'aes') {
            $this->security = true;

            $input = $this->getCryptor()->decryptMsg($this->query->msg_signature,
                            $this->query->nonce, $this->query->timestamp, $xmlInput);
        }

        return array_merge($_POST, (array) $input);
    }

    /**
     * 获取加密器
     *
     * @return Crypt
     */
    protected function getCryptor()
    {
        return Crypt::make($this->options->app_id,
                                $this->options->encodingAESKey, $this->options->token);
    }

    /**
     * 处理微信的请求
     *
     * @return mixed
     */
    protected function handleRequest()
    {
        if ($this->post->has('MsgId')) {
            return $this->handleMessage($this->post);
        } else if ($this->post->has('MsgType') && $this->post->MsgType == 'event') {
            return $this->handleEvent($this->post);
        }

        return false;
    }

    /**
     * 处理消息
     *
     * @param array $message
     *
     * @return mixed
     */
    protected function handleMessage($message)
    {
        if (!is_null($response = $this->call("message.*", [$message]))) {
            return $response;
        }

        return $this->call("message.{$message['MsgType']}", [$message]);
    }

    /**
     * 处理事件
     *
     * @param array $event
     *
     * @return mixed
     */
    protected function handleEvent($event)
    {
        if (!is_null($response = $this->call("event.*", [$event]))) {
            return $response;
        }

        return $this->call("event.{$event['Event']}", [$event]);
    }

    /**
     * 调用监听器
     *
     * @param string $key
     * @param array  $args
     *
     * @return mixed
     */
    protected function call($key, $args)
    {
        $handlers = (array) $this->listeners[$key];

        if (empty($handlers)) {
            return null;
        }

        foreach ($handlers as $handler) {
            if (!is_callable($handler)) {
                continue;
            }

            $res = call_user_func_array($handler, $args);

            if (!is_null($res)) {
                return $res;
            }
        }

        return null;
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
        if (in_array($method, $this->services)) {
            return $this->get($method);
        }
    }

    /**
     * 处理魔术调用
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        if (in_array($property, $this->services)) {
            return $this->get($property);
        }
    }

    /**
     * 防止序列化
     *
     * @return null
     */
    public function __sleep()
    {
        return null;
    }
}