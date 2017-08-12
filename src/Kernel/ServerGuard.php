<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\Raw as RawMessage;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Kernel\Traits\Observable;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServerGuard.
 *
 * @author overtrue <i@overtrue.me>
 */
class ServerGuard
{
    use Observable;

    /**
     * @var bool
     */
    protected $alwaysValidate = false;

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

        $response = $this->validate()->resolve();

        $this->app['logger']->debug('Server response created:', ['content' => $response->getContent()]);

        return $response;
    }

    /**
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function validate()
    {
        if (!$this->isSafeMode()) {
            return $this;
        }

        $params = [
            $this->app['config']['token'],
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
        $message = $this->parseMessage($this->app['request']->getContent(false));

        if (!is_array($message) || empty($message)) {
            throw new BadRequestException('No message received.');
        }

        if ($this->isSafeMode() && !empty($message['Encrypt'])) {
            $message = $this->app['encryptor']->decrypt(
                $message['Encrypt'],
                $message['MsgSignature'],
                $message['Nonce'],
                $message['TimeStamp']
            );

            return XML::parse($message);
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
        $result = $this->handleRequest();

        if ($this->shouldReturnRawResponse()) {
            return new Response($result['response']);
        }

        return new Response(
            $this->buildResponse($result['to'], $result['from'], $result['response']),
            200,
            ['Content-Type' => 'application/xml']
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

        return $this->buildReply($to, $from, $message);
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
            'to' => $message['FromUserName'] ?? '',
            'from' => $message['ToUserName'] ?? '',
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

        $response = $message->transformToXml($prepends);

        if ($this->isSafeMode()) {
            $this->app['logger']->debug('Messages safe mode is enabled.');
            $response = $this->app['encryptor']->encrypt($response);
        }

        return $response;
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
    protected function parseMessage($content)
    {
        try {
            if (stripos($content, '<') === 0) {
                $content = XML::parse($content);
            } else {
                // For mini-program JSON formats.
                // Convert to XML if the given string can be decode into a data array.
                $dataSet = json_decode($content, true);

                if ($dataSet && (JSON_ERROR_NONE === json_last_error())) {
                    $content = $dataSet;
                }
            }

            return (array) $content;
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
        if ($this->alwaysValidate) {
            return true;
        }

        return $this->app['request']->get('signature') && $this->app['request']->get('encrypt_type') === 'aes';
    }

    /**
     * @return bool
     */
    protected function shouldReturnRawResponse(): bool
    {
        return false;
    }
}
