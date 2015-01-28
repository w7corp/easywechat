<?php namespace Overtrue\Wechat;

use Exception;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\XML;
use Overtrue\Wechat\Utils\Crypt;
use Overtrue\Wechat\Traits\Loggable;
use Overtrue\Wechat\Traits\Instanceable;

class Server {

    use Loggable, Instanceable;

    protected $post;
    protected $request;
    protected $options;
    protected $listeners;
    protected $security = false;

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

        $response = $this->handleRequest();

        return $this->buildResponse($response);
    }

    /**
     * 生成回复内容
     *
     * @param mixed $response
     *
     * @return string
     */
    protected function buildResponse($response)
    {
        if (is_array($response)) {
            $response['ToUserName']   = $this->post->FromUserName;
            $response['FromUserName'] = $this->post->ToUserName;
error_log(json_encode($response));
            $xml = XML::build($response);

            header('content-type:text/xml');

            if ($this->security) {
                return $this->getCryptor()->encryptMsg($xml);
            }

            return $xml;
        }

        return strval($response);
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

        $input = XML::parse($xmlInput);

        if ($this->request->encrypt_type == 'aes') {
            $this->security = true;

            $input = $this->getCryptor()->decryptMsg($this->request->msg_signature,
                            $this->request->nonce, $this->request->timestamp, $xmlInput);
        }

        return array_merge($_POST, $input);
    }

    /**
     * 获取加密器
     *
     * @return Crypt
     */
    protected function getCryptor()
    {
        return Crypt::make($this->options->app_id,
                                $this->options->AESKey, $this->options->token);
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
    protected function handleMessage($message)
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
    protected function handleEvent($event)
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
}