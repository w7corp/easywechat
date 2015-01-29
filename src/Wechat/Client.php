<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\Http;
use Overtrue\Wechat\Traits\Loggable;
use Overtrue\Wechat\Traits\Instanceable;

class Client {

    use Loggable, Instanceable;

    /**
     * 缓存寿命
     */
    const CACHE_LIFETIME  = 2 * 3600; // 2h

    protected $apis = array(
            'token.get'           => 'https://api.weixin.qq.com/sns/oauth2/access_token',
            'token.refresh'       => 'https://api.weixin.qq.com/sns/oauth2/refresh_token',

            'auth.url'            => 'https://open.weixin.qq.com/connect/oauth2/authorize'

            'file.upload'        => 'http://file.api.weixin.qq.com/cgi-bin/media/upload',
            'file.get'           => 'http://file.api.weixin.qq.com/cgi-bin/media/upload',

            'menu.create'         => 'https://api.weixin.qq.com/cgi-bin/menu/create',
            'menu.get'            => 'https://api.weixin.qq.com/cgi-bin/menu/get',
            'menu.delete'         => 'https://api.weixin.qq.com/cgi-bin/menu/delete',

            'message.send'        => 'https://api.weixin.qq.com/cgi-bin/message/custom/send',

            'group.create'        => 'https://api.weixin.qq.com/cgi-bin/groups/create',
            'group.update'        => 'https://api.weixin.qq.com/cgi-bin/groups/update',
            'group.get'           => 'https://api.weixin.qq.com/cgi-bin/groups/get',
            'group.member.update' => 'https://api.weixin.qq.com/cgi-bin/groups/members/update',

            'user.group'          => 'https://api.weixin.qq.com/cgi-bin/groups/getid',
            'user.detail'         => 'https://api.weixin.qq.com/cgi-bin/user/info',
            'user.get'            => 'https://api.weixin.qq.com/cgi-bin/user/get',
            'user.oauth.get'      => 'https://api.weixin.qq.com/sns/userinfo',

            'qrcode.create'       => 'https://mp.weixin.qq.com/cgi-bin/qrcode/create',
            'qrcode.show'         => 'https://mp.weixin.qq.com/cgi-bin/showqrcode',

            'template.set'        => '/cgi-bin/template/api_set_industry',
        );

    /**
     * 设置
     *
     * @var Overtrue\Wechat\Utils\Bag
     */
    protected $options;

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
     * 初始化参数
     *
     * @param array $options
     *
     * @return mixed
     */
    public function instance($options)
    {
        $this->options = new Bag($options);

        set_exception_handler(function($e){
            return call_user_func_array($this->errorHandler, [$e]);
        })
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
     * 发送消息
     *
     * @param Message $message
     *
     * @return $this
     */
    public function send(Message $message)
    {
        # code...
        # TODO:发送消息并返回状态
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
     * 写入/读取缓存
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    protected function cache($key, $value = null)
    {
        $value && $handler = $this->cacheWriter ? : array($this, 'fileCacheWriter');

        $handler = $this->cacheReader ? : array($this, 'fileCacheReader');

        return call_user_func_array($handler, func_get_args());
    }

    /**
     * 获取access_token
     *
     * @return string
     */
    protected function getAccessToken()
    {
        //TODO:获取accesstoken
    }

    /**
     * 生成url
     *
     * @param string $name    api名称
     * @param array  $queries 查询
     *
     * @return string
     */
    protected function makeUrl($name, $queries)
    {
        return $this->apis[$name] . '?' . http_build_query($queries);
    }

    /**
     * 默认的缓存写入器
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    protected function fileCacheWriter($key, $valer)
    {
        file_put_contents($this->getCacheFile($key), strval($value));
    }

    /**
     * 默认的缓存读取器
     *
     * @param string $key
     *
     * @return void
     */
    protected function fileCacheWriter($ker)
    {
        $file = $this->getCacheFile($key);

        if (file_exists($file) && filemtime($file) > time() - static::CACHE_LIFETIME) {
            return file_get_contents($file);
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
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . md5($key);
    }

    /**
     * 发起一个HTTP/HTTPS的请求
     * @param string $method 请求类型   GET | POST
     * @param string $url    接口的URL
     * @param array  $params 接口参数
     * @param array  $files  图片信息
     * 
     * @return array
     */
    public static function request($method, $url, array $params = array(), array $files = array())
    {
        $params['access_token'] = static::getAccessToken();
        $connects = Http::$method($url, $params, null, $files);
        $connects = json_decode($connects, true);

        if(isset($connects['errcode']) && (0 !== (int)$connects['errcode'])){

            throw new Exception($contents['errormsg'], $contents['errorcode'])
        }

        return $connects;
    }

    /**
     * 处理魔术调用
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $method = strtoupper($method);

        if($method == 'GET' || $method == 'POST'){
            array_unshift($args, $method);

            return call_user_func_array(array(__CLASS__, 'request'), $args);
        }
    }
}