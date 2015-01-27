<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Util\Bag;
use Overtrue\Wechat\Trait\Loggable;

class Server {

    use Loggable;

    protected $options   = array();
    protected $listeners = array();
    protected $request   = array();
    protected static $instance  = null;

    public function __construct()
    {
        $this->listeners = new Bag;
    }

    /**
     * 监听事件
     *
     * @param string   $event
     * @param callback $function
     *
     * @return mixed
     */
    public function event($event, callback $function)
    {
        $this->listeners['event']->add($event, $function);
    }

    /**
     * 监听消息
     *
     * @param string   $type
     * @param callback $function
     *
     * @return string
     */
    public function message($type, callback $function)
    {
        $this->listeners['message']->add($type, $function);
    }

    /**
     * 应答微信的验证请求
     *
     * @return void|string
     */
    public function validation()
    {
        return $this->request->echostr;
    }

    /**
     * 开始运行
     *
     * @param array $options
     *
     * @return mixed
     */
    public function run($options)
    {
        $this->request = new Bag($_REQUEST);

        if (!$this->checkSignature()) {
            throw new Exception("Bad Request", 400);
        }

        //TODO
    }

    /**
     * 处理静态访问
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __callStatic($method, $args)
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        if (is_callable(static::$instance, $method)) {
            return call_user_func_array(array(static::$instance, $method), $args);
        }
    }
}