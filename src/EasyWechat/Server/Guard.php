<?php

/**
 * Guard.php.
 *
 * Part of EasyWeChat.
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

namespace EasyWeChat\Server;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Input;
use EasyWeChat\Encryption\Cryptor;
use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Message\MessageBuilder;
use EasyWeChat\Support\Collection;

/**
 * Class Guard.
 */
class Guard
{
    /**
     * 输入.
     *
     * @var \EasyWeChat\Support\Collection
     */
    protected $input;

    /**
     * 监听器.
     *
     * @var \EasyWeChat\Support\Collection
     */
    protected $listeners;

    /**
     * 允许的事件.
     *
     * @var array
     */
    protected $events = [
                         'received',
                         'served',
                         'responseCreated',
                        ];

    /**
     * Constructor.
     *
     * @param Input   $input
     * @param Cryptor $cryptor
     */
    public function __construct(Input $input, Cryptor $cryptor)
    {
        $this->listeners = new Collection();
        $this->input = $input;
    }

    /**
     * Add a listener.
     *
     * @param string          $target
     * @param string|callable $type
     * @param callable        $callback
     *
     * @return Guard
     *
     * @throws InvalidArgumentException
     */
    public function on($target, $type, $callback = null)
    {
        if (is_null($callback)) {
            $callback = $type;
            $type = '*';
        }

        if (!is_callable($callback)) {
            throw new InvalidArgumentException('The linstener is not callable.');
        }

        $type = strtolower($type);

        $listeners = $this->listeners->get("{$target}.{$type}") ?: [];

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
     * Handle and return response.
     *
     * @return mixed
     *
     * @throws BadRequestException
     */
    public function serve()
    {
        if ($this->input->has('echostr')) {
            return $this->input['echostr'];
        }

        return $this->response($this->handleRequest());
    }

    /**
     * Build response.
     *
     * @param mixed $response
     *
     * @return string
     */
    protected function response($response)
    {
        is_string($response) && $response = MessageBuilder::make('text')->with('content', $response);

        $return = null;

        if ($response instanceof AbstractMessage) {
            $response->from($this->input->get('ToUserName'))->to($this->input->get('FromUserName'));

            $this->call('responseCreated', [$response]);

            $return = $response->buildForReply();

            if ($this->input->isEncrypted()) {
                $return = $this->cryptor->encryptMsg(
                    $return,
                    $this->input->get('nonce'),
                    $this->input->get('timestamp')
                );
            }
        }

        $return = $this->call('served', [$return], $return);

        return $return;
    }

    /**
     * 处理微信的请求.
     *
     * @return mixed
     */
    protected function handleRequest()
    {
        $this->call('received', [$this->input]);

        if ($this->input->has('MsgType') && $this->input->get('MsgType') === 'event') {
            return $this->handleEvent($this->input);
        } elseif ($this->input->has('MsgId')) {
            return $this->handleMessage($this->input);
        }

        return '';
    }

    /**
     * 处理消息.
     *
     * @param Collection $message
     *
     * @return mixed
     */
    protected function handleMessage($message)
    {
        if (!is_null($response = $this->call('message.*', [$message]))) {
            return $response;
        }

        return $this->call("message.{$message['MsgType']}", [$message]);
    }

    /**
     * 处理事件.
     *
     * @param Collection $event
     *
     * @return mixed
     */
    protected function handleEvent($event)
    {
        if (!is_null($response = $this->call('event.*', [$event]))) {
            return $response;
        }

        $event['Event'] = strtolower($event['Event']);

        return $this->call("event.{$event['Event']}", [$event]);
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
} // end class

