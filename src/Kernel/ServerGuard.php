<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel;

use EasyWeChat\Kernel\Contracts\MessageInterface;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Raw as RawMessage;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Support\XML;
use EasyWeChat\Kernel\Traits\Observable;
use EasyWeChat\Kernel\Traits\ResponseCastable;
use Symfony\Component\HttpFoundation\Response;

/**
 * 1. url 里的 signature 只是将 token+nonce+timestamp 得到的签名，只是用于验证当前请求的，在公众号环境下一直有
 * 2. 企业号消息发送时是没有的，因为固定为完全模式，所以 url 里不会存在 signature, 只有 msg_signature 用于解密消息的
 */
class ServerGuard
{
    use Observable;
    use ResponseCastable;

    protected bool $alwaysValidate = false;

    public const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * @var array
     */
    public const MESSAGE_TYPE_MAPPING = [
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
        'miniprogrampage' => Message::MINIPROGRAM_PAGE,
    ];

    protected ServiceContainer $app;

    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;

        foreach ($this->app->extension->observers() as $observer) {
            call_user_func_array([$this, 'push'], $observer);
        }
    }

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

    public function validate(): static
    {
        if (!$this->alwaysValidate && !$this->isSafeMode()) {
            return $this;
        }

        if ($this->app['request']->get('signature') !== $this->signature([
                $this->getToken(),
                $this->app['request']->get('timestamp'),
                $this->app['request']->get('nonce'),
            ])) {
            throw new BadRequestException('Invalid request signature.', 400);
        }

        return $this;
    }

    public function forceValidate(): static
    {
        $this->alwaysValidate = true;

        return $this;
    }

    public function getMessage()
    {
        $message = $this->parseMessage($this->app['request']->getContent(false));

        if (!is_array($message) || empty($message)) {
            throw new BadRequestException('No message received.');
        }

        if ($this->isSafeMode() && !empty($message['Encrypt'])) {
            $message = $this->decryptMessage($message);

            // Handle JSON format.
            $dataSet = json_decode($message, true);

            if ($dataSet && (JSON_ERROR_NONE === json_last_error())) {
                return $dataSet;
            }

            $message = XML::parse($message);
        }

        return $this->detectAndCastResponseToType($message, $this->app->config->get('response_type'));
    }

    protected function resolve(): Response
    {
        $result = $this->handleRequest();

        if ($this->shouldReturnRawResponse()) {
            $response = new Response($result['response']);
        } else {
            $response = new Response(
                $this->buildResponse($result['to'], $result['from'], $result['response']),
                200,
                ['Content-Type' => 'application/xml']
            );
        }

        $this->app->events->dispatch(new Events\ServerGuardResponseCreated($response));

        return $response;
    }

    protected function getToken(): string
    {
        return $this->app['config']['token'];
    }

    public function buildResponse(string $to, string $from, $message)
    {
        if (empty($message) || self::SUCCESS_EMPTY_RESPONSE === $message) {
            return self::SUCCESS_EMPTY_RESPONSE;
        }

        if ($message instanceof RawMessage) {
            return $message->get('content', self::SUCCESS_EMPTY_RESPONSE);
        }

        if (is_string($message) || is_numeric($message)) {
            $message = new Text((string) $message);
        }

        if (is_array($message) && reset($message) instanceof NewsItem) {
            $message = new News($message);
        }

        if (!($message instanceof Message)) {
            throw new InvalidArgumentException(sprintf('Invalid Messages type "%s".', gettype($message)));
        }

        return $this->buildReply($to, $from, $message);
    }

    protected function handleRequest(): array
    {
        $castedMessage = $this->getMessage();

        $messageArray = $this->detectAndCastResponseToType($castedMessage, 'array');

        $response = $this->dispatch(self::MESSAGE_TYPE_MAPPING[$messageArray['MsgType'] ?? $messageArray['msg_type'] ?? 'text'], $castedMessage);

        return [
            'to' => $messageArray['FromUserName'] ?? '',
            'from' => $messageArray['ToUserName'] ?? '',
            'response' => $response,
        ];
    }

    protected function buildReply(string $to, string $from, MessageInterface $message): string
    {
        $prepends = [
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => $message->getType(),
        ];

        $response = $message->transformToXml($prepends);

        if ($this->isSafeMode()) {
            $this->app['logger']->debug('Messages safe mode is enabled.');
            $response = $this->app['encryptor']->encrypt($response);
        }

        return $response;
    }

    protected function signature(array $params): string
    {
        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    protected function parseMessage(string $content): array
    {
        try {
            if (0 === stripos($content, '<')) {
                $content = XML::parse($content);
            } else {
                // Handle JSON format.
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

    protected function isSafeMode(): bool
    {
        return $this->app['request']->get('signature') && 'aes' === $this->app['request']->get('encrypt_type');
    }

    protected function shouldReturnRawResponse(): bool
    {
        return false;
    }

    protected function decryptMessage(array $message)
    {
        return $message = $this->app['encryptor']->decrypt(
            $message['Encrypt'],
            $this->app['request']->get('msg_signature'),
            $this->app['request']->get('nonce'),
            $this->app['request']->get('timestamp')
        );
    }
}
