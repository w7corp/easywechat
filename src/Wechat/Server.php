<?php namespace Overtrue\Wechat;

use Overtrue\Wechat\Util\Bag;
use Overtrue\Wechat\Trait\Loggable;

class Server {

    use Loggable;

    protected $options   = array();
    protected $listeners = array();

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
     * @param boolean $return 是否返回echostr,默认直接输出
     *
     * @return void|string
     */
    public function validation($return = false)
    {
        if ($this->checkSignature()) {
            return false;
        }

        $msg = Arr::get($_GET, 'echostr');

        if ($return) {
            return $msg;
        }

        exit($msg);
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
        # code...
    }
}