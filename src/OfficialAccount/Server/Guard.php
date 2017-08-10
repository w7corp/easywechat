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

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Raw as RawMessage;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Kernel\Traits\Observable;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Guard.
 *
 * @author overtrue <i@overtrue.me>
 */
class Guard
{
    use Observable;

    /**
     * Empty string.
     */
    const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * @var array
     */
    const MESSAGE_TYPE_MAPPING = [
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
     * @var \EasyWeChat\Kernel\ServiceContainer
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    /**
     * Handle and return response.
     *
     * @return Response
     *
     * @throws BadRequestException
     */
    public function serve(): Response
    {
        $this->app['logger']->debug('Request received:', [
            'method' => $this->app['request']->getMethod(),
            'uri' => $this->app['request']->getUri(),
            'content-type' => $this->app['request']->getContentType(),
            'content' => $this->app['request']->getContent(),
        ]);

        $response = $this->validate($this->app['config']['token'] ?? '')->resolve();

        $this->app['logger']->debug('Server response created:', ['content' => $response->getContent()]);

        return $response;
    }

    /**
     * @param string $token
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function validate(string $token)
    {
        if (!$this->app['request']->get('signature')) {
            return $this;
        }

        if ($this->app['request']->getRealMethod() === 'GET' && $this->app['request']->get('echostr')) {
            return $this;
        }

        $params = [
            $token,
            $this->app['request']->get('timestamp'),
            $this->app['request']->get('nonce'),
        ];

        if ($this->app['request']->get('signature') !== $this->signature($params)) {
            throw new BadRequestException('Invalid request signature.', 400);
        }

        return $this;
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
        $message = $this->parseMessageFromRequest($this->app['request']->getContent(false));

        if (!is_array($message) || empty($message)) {
            throw new BadRequestException('No message received.');
        }

        return $message;
    }

    /**
     * Resolve server request and return the response.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function resolve(): Response
    {
        if ($str = $this->app['request']->get('echostr')) {
            $this->app['logger']->debug("Output 'echostr' is '$str'.");

            return new Response($str);
        }

        $result = $this->handleRequest();

        return new Response(
            $this->buildResponse($result['to'], $result['from'], $result['response'])
        );
    }

    /**
     * @param string                                                   $to
     * @param string                                                   $from
     * @param \EasyWeChat\Kernel\Contracts\MessageInterface|string|int $message
     *
     * @return mixed|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function buildResponse(string $to, string $from, $message)
    {
        if (empty($message) || $message === self::SUCCESS_EMPTY_RESPONSE) {
            return self::SUCCESS_EMPTY_RESPONSE;
        }

        if ($message instanceof RawMessage) {
            return $message->get('content', self::SUCCESS_EMPTY_RESPONSE);
        }

        if (is_string($message) || is_numeric($message)) {
            $message = new Text((string) $message);
        }

        if (!$this->isMessage($message)) {
            throw new InvalidArgumentException(sprintf('Invalid Messages type "%s".', gettype($message)));
        }

        $response = $this->buildReply($to, $from, $message);

        if ($this->isSafeMode()) {
            $this->app['logger']->debug('Messages safe mode is enabled.');
            $response = $this->app['encryptor']->encrypt(
                $response,
                $this->app['request']->get('nonce'),
                $this->app['request']->get('timestamp')
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
     * Handle request.
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \EasyWeChat\OfficialAccount\Server\BadRequestException
     */
    protected function handleRequest(): array
    {
        $message = $this->getMessage();

        $response = $this->dispatch(self::MESSAGE_TYPE_MAPPING[$message['MsgType'] ?? 'text'], $message);

        return [
            'to' => $message['FromUserName'],
            'from' => $message['ToUserName'],
            'response' => $response,
        ];
    }

    /**
     * Build reply XML.
     *
     * @param string                                                     $to
     * @param string                                                     $from
     * @param \EasyWeChat\Kernel\Contracts\MessageInterface|string|array $message
     *
     * @return string
     */
    protected function buildReply(string $to, string $from, $message): string
    {
        $prepends = [
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => is_array($message) ? current($message)->getType() : $message->getType(),
        ];

        return $message->transformToXml($prepends);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected function signature(array $params)
    {
        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    /**
     * Parse message array from raw php input.
     *
     * @param string|resource $content
     *
     * @return array
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    protected function parseMessageFromRequest($content)
    {
        $content = strval($content);

        try {
            // For mini-program JSON formats.
            // Convert to XML if the given string can be decode into a data array.
            $dataSet = json_decode($content, true);

            if ($dataSet && (JSON_ERROR_NONE === json_last_error())) {
                $content = XML::build($dataSet);
            }

            if ($this->isSafeMode()) {
                $message = $this->app['encryptor']->decrypt(
                    $this->app['request']->get('msg_signature'),
                    $this->app['request']->get('nonce'),
                    $this->app['request']->get('timestamp'),
                    $content
                );
            } else {
                $message = XML::parse($content);
            }

            return $message;
        } catch (\Exception $e) {
            throw new BadRequestException(sprintf('Invalid message content:(%s) %s', $e->getCode(), $e->getMessage()), $e->getCode());
        }
    }

    /**
     * Check the request message safe mode.
     *
     * @return bool
     */
    protected function isSafeMode(): bool
    {
        return $this->app['request']->get('encrypt_type') === 'aes';
    }
}
