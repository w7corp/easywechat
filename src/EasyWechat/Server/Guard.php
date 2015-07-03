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
use EasyWeChat\Message\Text;
use EasyWeChat\Support\Collection;

/**
 * Class Guard.
 */
class Guard
{
    /**
     * Empty string.
     */
    const EMPTY_STRING = '';

    /**
     * Input.
     *
     * @var Collection
     */
    protected $input;

    /**
     * Listeners.
     *
     * @var Collection
     */
    protected $listeners;

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
     * Add a event listener.
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
     * Add a message listener.
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
        if (is_string($response)) {
            $message = new Text(['content' => $response]);
        }

        $return = '';

        if ($this->isMessage($response)) {
            $return = $this->buildReply(
                    $this->input->get('ToUserName'),
                    $this->input->get('FromUserName'),
                    $response
            );

            if ($this->input->isEncrypted()) {
                $return = $this->cryptor->encryptMsg(
                    $return,
                    $this->input->get('nonce'),
                    $this->input->get('timestamp')
                );
            }
        }

        return $return;
    }

    /**
     * Wether response is message.
     *
     * @param mixed $response
     *
     * @return bool
     */
    protected function isMessage($response)
    {
        return is_subclass_of($response, 'EasyWeChat\Message\AbstractMessage');
    }

    /**
     * Handle request.
     *
     * @return mixed
     */
    protected function handleRequest()
    {
        if ($this->input->has('MsgType') && $this->input->get('MsgType') === 'event') {
            return $this->handleEvent($this->input);
        } elseif ($this->input->has('MsgId')) {
            return $this->handleMessage($this->input);
        }

        return self::EMPTY_STRING;
    }

    /**
     * Handle message.
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
     * Handle event message.
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
     * Build reply XML.
     *
     * @param string           $to
     * @param string           $from
     * @param MessageInterface $message
     *
     * @return string
     */
    protected function buildReply($to, $from, $message)
    {
        $base = [
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => $message->getType(),
        ];

        return XML::build(array_merge($base, $this->transformer->transform($message)));
    }

    /**
     * Call listener.
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
} // end class

