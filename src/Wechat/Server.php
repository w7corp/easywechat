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
 * Server.php.
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

use Overtrue\Wechat\Messages\BaseMessage;
use Overtrue\Wechat\Utils\Bag;
use Overtrue\Wechat\Utils\XML;

/**
 * 服务端.
 */
class Server
{
    /**
     * 应用ID.
     *
     * @var string
     */
    protected $appId;

    /**
     * token.
     *
     * @var string
     */
    protected $token;

    /**
     * encodingAESKey.
     *
     * @var string
     */
    protected $encodingAESKey;

    /**
     * 输入.
     *
     * @var \Overtrue\Wechat\Utils\Bag
     */
    protected $input;

    /**
     * 监听器.
     *
     * @var \Overtrue\Wechat\Utils\Bag
     */
    protected $listeners;

    /**
     * 是否为加密模式.
     *
     * @var bool
     */
    protected $security = false;

    /**
     * 允许的事件.
     *
     * @var array
     */
    protected $events = array(
                         'received',
                         'served',
                         'responseCreated',
                        );

    /**
     * constructor.
     *
     * @param string $appId
     * @param string $token
     * @param string $encodingAESKey
     */
    public function __construct($appId, $token, $encodingAESKey = null)
    {
        $this->listeners = new Bag();
        $this->appId = $appId;
        $this->token = $token;
        $this->encodingAESKey = $encodingAESKey;
    }

    /**
     * 监听.
     *
     * @param string          $target
     * @param string|callable $type
     * @param callable        $callback
     *
     * @return Server
     */
    public function on($target, $type, $callback = null)
    {
        if (is_null($callback)) {
            $callback = $type;
            $type = '*';
        }

        if (!is_callable($callback)) {
            throw new Exception("$callback 不是一个可调用的函数或方法");
        }

        $type = strtolower($type);

        $listeners = $this->listeners->get("{$target}.{$type}") ?: array();

        array_push($listeners, $callback);

        $this->listeners->set("{$target}.{$type}", $listeners);

        return $this;
    }

    /**
     * 监听事件.
     *
     * @param string|callable $type
     * @param callable        $callback
     *
     * @return Server
     */
    public function event($type, $callback = null)
    {
        return $this->on('event', $type, $callback);
    }

    /**
     * 监听消息.
     *
     * @param string|callable $type
     * @param callable        $callback
     *
     * @return Server
     */
    public function message($type, $callback = null)
    {
        return $this->on('message', $type, $callback);
    }

    /**
     * handle服务端并返回字符串内容.
     *
     * @return mixed
     */
    public function serve()
    {
        $this->prepareInput();

        $input = array(
                  $this->token,
                  $this->input->get('timestamp'),
                  $this->input->get('nonce'),
                 );

        if ($this->input->get('signature')
            && $this->signature($input) !== $this->input->get('signature')
        ) {
            throw new Exception('Bad Request', 400);
        }

        if ($this->input->get('echostr')) {
            return strip_tags($this->input['echostr']);
        }

        return $this->response($this->handleRequest());
    }

    /**
     * 初始化POST请求数据.
     *
     * @return Bag
     */
    protected function prepareInput()
    {
        if ($this->input instanceof Bag) {
            return;
        }

        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
                $xmlInput = $GLOBALS['HTTP_RAW_POST_DATA'];
            } else {
                $xmlInput = file_get_contents('php://input');
            }

            if (empty($_REQUEST['echostr']) && empty($xmlInput) && !empty($_REQUEST['signature'])) {
                throw new Exception('没有读取到消息 XML，请在 php.ini 中打开 always_populate_raw_post_data=On', 500);
            }
        } else {
            $xmlInput = file_get_contents('php://input');
        }

        $input = XML::parse($xmlInput);

        if (!empty($_REQUEST['encrypt_type'])
            && $_REQUEST['encrypt_type'] === 'aes'
        ) {
            $this->security = true;

            $input = $this->getCrypt()->decryptMsg(
                $_REQUEST['msg_signature'],
                $_REQUEST['nonce'],
                $_REQUEST['timestamp'],
                $xmlInput
            );
        }

        $this->input = new Bag(array_merge($_REQUEST, (array) $input));
    }

    /**
     * 获取输入.
     *
     * @param array $input
     */
    public function setInput(array $input)
    {
        $this->input = new Bag($input);
    }

    /**
     * 获取Crypt服务
     *
     * @return Crypt
     */
    protected function getCrypt()
    {
        static $crypt;

        if (!$crypt) {
            if (empty($this->encodingAESKey) || empty($this->token)) {
                throw new Exception("加密模式下 'encodingAESKey' 与 'token' 都不能为空！");
            }

            $crypt = new Crypt($this->appId, $this->token, $this->encodingAESKey);
        }

        return $crypt;
    }

    /**
     * 生成回复内容.
     *
     * @param mixed $response
     *
     * @return string
     */
    protected function response($response)
    {
        if (empty($response)) {
            return '';
        }

        is_string($response) && $response = Message::make('text')->with('content', $response);

        $return = '';

        if ($response instanceof BaseMessage) {
            $response->from($this->input->get('ToUserName'))->to($this->input->get('FromUserName'));

            $this->call('responseCreated', array($response));

            $return = $response->buildForReply();

            if ($this->security) {
                $return = $this->getCrypt()->encryptMsg(
                    $return,
                    $this->input->get('nonce'),
                    $this->input->get('timestamp')
                );
            }
        }

        $return = $this->call('served', array($return), $return);

        return $return;
    }

    /**
     * 处理微信的请求
     *
     * @return mixed
     */
    protected function handleRequest()
    {
        $this->call('received', array($this->input));

        if ($this->input->get('MsgType') && $this->input->get('MsgType') === 'event') {
            return $this->handleEvent($this->input);
        } elseif ($this->input->get('MsgId')) {
            return $this->handleMessage($this->input);
        }

        return false;
    }

    /**
     * 处理消息.
     *
     * @param Bag $message
     *
     * @return mixed
     */
    protected function handleMessage($message)
    {
        if (!is_null($response = $this->call('message.*', array($message)))) {
            return $response;
        }

        return $this->call("message.{$message['MsgType']}", array($message));
    }

    /**
     * 处理事件.
     *
     * @param Bag $event
     *
     * @return mixed
     */
    protected function handleEvent($event)
    {
        if (!is_null($response = $this->call('event.*', array($event)))) {
            return $response;
        }

        $event['Event'] = strtolower($event['Event']);

        return $this->call("event.{$event['Event']}", array($event));
    }

    /**
     * 检查微信签名有效性.
     *
     * @param array $input
     */
    protected function signature($input)
    {
        sort($input, SORT_STRING);

        return sha1(implode($input));
    }

    /**
     * 调用监听器.
     *
     * @param string      $key
     * @param array       $args
     * @param string|null $default
     *
     * @return mixed
     */
    protected function call($key, $args, $default = null)
    {
        $handlers = (array) $this->listeners[$key];

        foreach ($handlers as $handler) {
            if (!is_callable($handler)) {
                continue;
            }

            $res = call_user_func_array($handler, $args);

            if (!empty($res)) {
                return $res;
            }
        }

        return $default;
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
        if (in_array($method, $this->events, true)) {
            $callback = array_shift($args);

            is_callable($callback) && $this->listeners->set($method, $callback);

            return;
        }
    }

    /**
     * 直接返回以字符串形式输出时.
     *
     * @return string
     */
    public function __toString()
    {
        return ''.$this->serve();
    }
}
