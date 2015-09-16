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

use EasyWeChat\Core\Exceptions\FaultException;
use EasyWeChat\Core\Exceptions\InvalidArgumentException;
use EasyWeChat\Core\Exceptions\RuntimeException;
use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\Message\AbstractMessage;
use EasyWeChat\Message\Text;
use EasyWeChat\Support\Collection;
use EasyWeChat\Support\XML;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Guard.
 */
class Guard
{
    /**
     * Empty string.
     */
    const EMPTY_STRING = 'success';

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Encryptor instance.
     *
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * Event listeners.
     *
     * @var string|callable
     */
    protected $eventListener;

    /**
     * Message listener.
     *
     * @var string|callable
     */
    protected $messageListener;

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle and return response.
     *
     * @return Response
     *
     * @throws BadRequestException
     */
    public function serve()
    {
        if ($str = $this->request->get('echostr')) {
            return new Response($str);
        }

        $result = $this->handleRequest();

        return new Response($this->buildResponse($result['to'], $result['from'], $result['response']));
    }

    /**
     * Validation request params.
     *
     * @param string $token
     *
     * @throws FaultException
     */
    public function validate($token)
    {
        $params = [
            $token,
            $this->request->get('timestamp'),
            $this->request->get('nonce'),
        ];

        if ($this->request->get('signature') !== $this->signature($params)) {
            throw new FaultException('Invalid request signature.', 400);
        }
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

        $this->messageListener = $callback;

        return $this;
    }

    /**
     * Return the message listener.
     *
     * @return string
     */
    public function getMessageListener()
    {
        return $this->messageListener;
    }

    /**
     * Set Encryptor
     *
     * @param Encryptor $encryptor
     *
     * @return Guard
     */
    public function setEncryptor(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;

        return $this;
    }

    /**
     * Return the encryptor instance.
     *
     * @return Encryptor
     */
    public function getEncryptor()
    {
        return $this->encryptor;
    }

    /**
     * Build response.
     *
     * @param mixed $message
     *
     * @return string
     */
    protected function buildResponse($to, $from, $message)
    {
        $response = self::EMPTY_STRING;

        if (empty($message)) {
            return $response;
        }

        if (is_string($message)) {
            $message = new Text(['content' => $message]);
        }

        if ($this->isMessage($message)) {
            $response = $this->buildReply($to, $from, $message);

            if ($this->isSafeMode()) {
                $response = $this->encryptor->encryptMsg(
                    $response,
                    $this->request->get('nonce'),
                    $this->request->get('timestamp')
                );
            }
        }

        return $response;
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
        return is_subclass_of($response, AbstractMessage::class);
    }

    /**
     * Handle request.
     *
     * @return array
     *
     * @throws \EasyWeChat\Core\Exceptions\RuntimeException
     * @throws \EasyWeChat\Server\BadRequestException
     */
    protected function handleRequest()
    {
        $message = $this->parseMessageFromRequest($this->request->getContent());

        if (empty($message)) {
            throw new BadRequestException('Invalid request.');
        }

        $response = $this->handleMessage($message);

        return [
            'to' => $message['FromUserName'],
            'from' => $message['ToUserName'],
            'response' => $response,
        ];
    }

    /**
     * Handle message.
     *
     * @param array $message
     *
     * @return mixed
     */
    protected function handleMessage($message)
    {
        $message = new Collection($message);

        $response = false;

        if ($message->get('MsgType') && $message->get('MsgType') === 'event' && $this->eventListener) {
            $response = call_user_func_array($this->eventListener, [$message]);
        } elseif (!empty($message['MsgId']) && $this->messageListener) {
            $response = call_user_func_array($this->messageListener, [$message]);
        }

        return $response;
    }

    /**
     * Build reply XML.
     *
     * @param string          $to
     * @param string          $from
     * @param AbstractMessage $message
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

        $transformer = new Transformer();

        return XML::build(array_merge($base, $transformer->transform($message)));
    }

    /**
     * Get signature.
     *
     * @param array $request
     *
     * @return string
     */
    protected function signature($request)
    {
        sort($request, SORT_STRING);

        return sha1(implode($request));
    }

    /**
     * Parse message array from raw php input.
     *
     * @param string|resource $content
     *
     * @return array
     *
     * @throws \EasyWeChat\Core\Exceptions\RuntimeException
     * @throws \EasyWeChat\Encryption\EncryptionException
     */
    protected function parseMessageFromRequest($content)
    {
        if ($this->isSafeMode()) {
            if (!$this->encryptor) {
                throw new RuntimeException('Safe mode Encryptor is necessary.');
            }

            $message = $this->encryptor->decryptMsg(
                $this->request->get('msg_signature'),
                $this->request->get('nonce'),
                $this->request->get('timestamp'),
                $content
            );
        } else {
            $message = XML::parse($content);
        }

        return $message;
    }

    /**
     * Check the request message safe mode.
     *
     * @return bool
     */
    private function isSafeMode()
    {
        return $this->request->get('encrypt_type') && $this->request->get('encrypt_type') === 'aes';
    }
}
