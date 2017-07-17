<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Raw as RawMessage;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Support\Collection;
use EasyWeChat\Kernel\Support\Log;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\OfficialAccount\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Guard.
 *
 * @author overtrue <i@overtrue.me>
 */
class Guard
{
    /**
     * Empty string.
     */
    const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string|callable
     */
    protected $messageHandler;

    /**
     * @var int
     */
    protected $messageFilter;

    /**
     * @var array
     */
    protected $messageTypeMapping = [
        'text' => Message::TEXT,
        'image' => Message::IMAGE,
        'voice' => Message::VOICE,
        'video' => Message::VIDEO,
        'shortvideo' => Message::SHORT_VIDEO,
        'location' => Message::LOCATION,
        'link' => Message::LINK,
        'device_event' => Message::DEVICE_EVENT,
        'device_text' => Message::DEVICE_TEXT,
        'event' => Message::EVENT,
        'file' => Message::FILE,
    ];

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var \EasyWeChat\OfficialAccount\Application
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\OfficialAccount\Application   $app
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Application $app, Request $request = null)
    {
        $this->app = $app;
        $this->request = $request ?: Request::createFromGlobals();
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
        Log::debug('Request received:', [
            'Method' => $this->request->getMethod(),
            'URI' => $this->request->getRequestUri(),
            'Query' => $this->request->getQueryString(),
            'Protocal' => $this->request->server->get('SERVER_PROTOCOL'),
            'Content' => $this->request->getContent(),
        ]);

        $response = $this->validate($this->app['config']['token'])->resolve();

        Log::debug('Server response created:', compact('response'));

        return $response;
    }

    /**
     * Validation request params.
     *
     * @param string $token
     *
     * @return $this
     *
     * @throws Exception
     */
    public function validate($token)
    {
        $params = [
            $token,
            $this->request->get('timestamp'),
            $this->request->get('nonce'),
        ];

        if (!$this->debug && $this->request->get('signature') !== $this->signature($params)) {
            throw new Exception('Invalid request signature.', 400);
        }

        return $this;
    }

    /**
     * Enable/Disable debug mode.
     *
     * @param bool $debug
     *
     * @return $this
     */
    public function debug($debug = true)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * Resolve server request and return the response.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resolve()
    {
        if ($str = $this->request->get('echostr')) {
            Log::debug("Output 'echostr' is '$str'.");

            return new Response($str);
        }

        $result = $this->handleRequest();

        return new Response(
            $this->buildResponse($result['to'], $result['from'], $result['response'])
        );
    }

    /**
     * Add a event listener.
     *
     * @param callable $callback
     * @param int      $option
     *
     * @return Guard
     *
     * @throws InvalidArgumentException
     */
    public function setMessageHandler($callback = null, $option = self::ALL_MSG)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Argument #2 is not callable.');
        }

        $this->messageHandler = $callback;
        $this->messageFilter = $option;

        return $this;
    }

    /**
     * Return the message listener.
     *
     * @return string
     */
    public function getMessageHandler()
    {
        return $this->messageHandler;
    }

    /**
     * Request getter.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Request setter.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Build response.
     *
     * @param $to
     * @param $from
     * @param mixed $message
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    protected function buildResponse($to, $from, $message)
    {
        if (empty($message) || $message === self::SUCCESS_EMPTY_RESPONSE) {
            return self::SUCCESS_EMPTY_RESPONSE;
        }

        if ($message instanceof RawMessage) {
            return $message->get('content', self::SUCCESS_EMPTY_RESPONSE);
        }

        if (is_string($message) || is_numeric($message)) {
            $message = new Text(['content' => $message]);
        }

        if (!$this->isMessage($message)) {
            $messageType = gettype($message);
            throw new InvalidArgumentException("Invalid Messages type .'{$messageType}'");
        }

        $response = $this->buildReply($to, $from, $message);

        if ($this->isSafeMode()) {
            Log::debug('Messages safe mode is enable.');
            $response = $this->app['encryptor']->encrypt(
                $response,
                $this->request->get('nonce'),
                $this->request->get('timestamp')
            );
        }

        return $response;
    }

    /**
     * Whether response is message.
     *
     * @param mixed $message
     *
     * @return bool
     */
    protected function isMessage($message)
    {
        if (is_array($message)) {
            foreach ($message as $element) {
                if (!($element instanceof Message)) {
                    return false;
                }
            }

            return true;
        }

        return $message instanceof Message;
    }

    /**
     * Get request message.
     *
     * @return array
     *
     * @throws BadRequestException
     */
    public function getMessage()
    {
        $message = $this->parseMessageFromRequest($this->request->getContent(false));

        if (!is_array($message) || empty($message)) {
            throw new BadRequestException('No message received.');
        }

        return $message;
    }

    /**
     * Handle request.
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \EasyWeChat\OfficialAccount\Server\BadRequestException
     */
    protected function handleRequest()
    {
        $message = $this->getMessage();
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
    protected function handleMessage(array $message)
    {
        $handler = $this->messageHandler;

        if (!is_callable($handler)) {
            Log::debug('No handler enabled.');

            return null;
        }

        Log::debug('Messages detail:', $message);

        $message = new Collection($message);

        $type = $this->messageTypeMapping[$message->get('MsgType')];

        $response = null;

        if ($this->messageFilter & $type) {
            $response = call_user_func_array($handler, [$message]);
        }

        return $response;
    }

    /**
     * Build reply XML.
     *
     * @param string  $to
     * @param string  $from
     * @param Message $message
     *
     * @return string
     */
    protected function buildReply($to, $from, $message)
    {
        $base = [
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => is_array($message) ? current($message)->getType() : $message->getType(),
        ];

        $transformer = new MessageTransformer();

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
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \EasyWeChat\OfficialAccount\Encryption\EncryptionException
     *
     * @return array
     */
    protected function parseMessageFromRequest($content)
    {
        $content = strval($content);

        $dataSet = json_decode($content, true);
        if ($dataSet && (JSON_ERROR_NONE === json_last_error())) {
            // For mini-program JSON formats.
            // Convert to XML if the given string can be decode into a data array.
            $content = XML::build($dataSet);
        }

        if ($this->isSafeMode()) {
            $message = $this->app['encryptor']->decrypt(
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
    protected function isSafeMode()
    {
        return $this->request->get('encrypt_type') && $this->request->get('encrypt_type') === 'aes';
    }
}
