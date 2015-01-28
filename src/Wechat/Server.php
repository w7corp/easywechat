<?php namespace Overtrue\Wechat;

use Exception;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Traits\Loggable;
use Overtrue\Wechat\Traits\Instanceable;

class Server {

    use Loggable, Instanceable;

    protected $post;
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
        $this->post      = new Bag($this->getPost());
    }

    /**
     * 监听事件
     *
     * @param string   $event
     * @param callable $function
     *
     * @return mixed
     */
    public function event($event, $function)
    {
        $this->listeners->add("event.{$event}", $function);
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
     * 获取POST请求数据
     *
     * @return array
     */
    protected function getPost()
    {
        $xmlInput = !empty($GLOBALS["HTTP_RAW_POST_DATA"])
                ? $GLOBALS["HTTP_RAW_POST_DATA"] : file_get_contents("php://input");

        return array_merge($_POST, XML::parse($xmlInput));
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
    public function handleMessage($message)
    {
        return $this->call("message.{$message['MsgType']}", [$message]);
    }

    /**
     * 处理事件
     *
     * @param array $event
     *
     * @return mixed
     */
    public function handleEvent($event)
    {
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
    public function call($key, $args)
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
}