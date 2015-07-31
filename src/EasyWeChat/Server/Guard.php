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
use EasyWeChat\Message\Text;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\XML;

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
     * Event listeners.
     *
     * @var string
     */
    protected $eventListener;

    /**
     * Message listener.
     *
     * @var string
     */
    protected $messageListener;

    /**
     * Constructor.
     *
     * @param Input $input
     */
    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    /**
     * Add a event listener.
     *
     * @param callable $callback
     *
     * @return Guard
     *
     * @throws InvalidArgumentException
     */
    public function setEventListener($callback = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Argument #2 is not callable.');
        }

        $this->eventListener = $callback;

        return $this;
    }

    /**
     * Return the event listener.
     *
     * @return string
     */
    public function getEventListener()
    {
        return $this->eventListener;
    }

    /**
     * Add a event listener.
     *
     * @param callable $callback
     *
     * @return Guard
     *
     * @throws InvalidArgumentException
     */
    public function setMessageListener($callback = null)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Argument #2 is not callable.');
        }

        $this->eventListener = $callback;

        return $this;
    }

    /**
     * Return the message listener.
     *
     * @return string
     */
    public function getMessageListener()
    {
        return $this->eventListener;
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
            $response = new Text(['content' => $response]);
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
     * Whether response is message.
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
            $response = $this->handleEvent($this->input);
        } elseif ($this->input->has('MsgId')) {
            $response = $this->handleMessage($this->input);
        }

        return $response ?: self::EMPTY_STRING;
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
        if ($this->messageListener) {
            return call_user_func_array($this->messageListener, [$message]);
        }

        return false;
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
        if ($this->eventListener) {
            return call_user_func_array($this->eventListener, [$event]);
        }

        return false;
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
}
