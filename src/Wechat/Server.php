<?php namespace Overtrue\Wechat;

use Exception;
use Overtrue\Wechat\Util\Bag;
use Overtrue\Wechat\Traits\Loggable;
use Overtrue\Wechat\Traits\Instanceable;

class Server {

    use Loggable, Instanceable;

    protected $request;
    protected $options;
    protected $listeners;

    /**
     * 初始化参数
     *
     * @param array $options
     *
     * @return mixed
     */
    public function instance($options)
    {
        $this->listeners = new Bag;
        $this->options   = new Bag($options);
        $this->request   = new Bag($_REQUEST);
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
     * 开始运行
     *
     * @param array $options
     *
     * @return mixed
     */
    public function run()
    {
        if (!$this->checkSignature()) {
            throw new Exception("Bad Request", 400);
        }

        return $this->handleRequest();
    }

    /**
     * 应答微信的验证请求
     *
     * @return void|string
     */
    protected function validation()
    {
        return $this->request->echostr;
    }

    /**
     * 检查微信签名有效性
     */
    protected function checkSignature()
    {
        $input = array(
                $this->options->token,
                $this->request->timestamp,
                $this->request->nonce,
              );

        sort($input, SORT_STRING);

        return sha1(implode($input)) === $this->request->signature;
    }

    /**
     * 处理微信的请求
     *
     * @return string
     */
    protected function handleRequest()
    {
        if ($this->request->has('echostr')) {
            return $this->validation();
        }

        //TODO:其它
    }
}