<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Server;

use EasyWeChat\Kernel\Contracts\MessageInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Messages\Message;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Raw as RawMessage;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Server\Handlers\MessageValidationHandler;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Kernel\Traits\Observable;
use Symfony\Component\HttpFoundation\Response;
use EasyWeChat\Kernel\Server\Request as ServerRequest;
use EasyWeChat\Kernel\Server\Response as ServerResponse;
use EasyWeChat\Kernel\Server\Message as ServerMessage;

class BaseServer
{
    use Observable;

    /**
     * @var \EasyWeChat\Kernel\Server\Request
     */
    public ServerRequest $request;

    /**
     * @var \EasyWeChat\Kernel\Server\Message|null
     */
    public ?ServerMessage $message = null;

    /**
     * Server constructor.
     *
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function __construct(
        public ServiceContainer $app
    ) {
        foreach (
            $this->app->extension->observers() as $observer
        ) {
            \array_map([$this, 'withHandler'], $observer);
        }

        $this->request = ServerRequest::createFromServer($this);
        $this->message = $this->request->getMessage();

        $this->withHandlers(
            [
                MessageValidationHandler::class,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|\EasyWeChat\Kernel\Server\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function process(): Response|ServerResponse
    {
        $response = $this->handle($this->message->toArray());

        if ($this->shouldReturnRawResponse()) {
            return new ServerResponse($response);
        }

        $response = $this->buildResponse($response);

        return
            ServerResponse::reply(
                $this->buildReply($this->message->to, $this->message->from, $response)
            );
    }

    /**
     * @return false
     */
    protected function shouldReturnRawResponse(): bool
    {
        return false;
    }

    /**
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function withoutMessageValidationHandler(): static
    {
        return $this->withoutHandler(MessageValidationHandler::class);
    }

    /**
     * @param string $type
     * @param        $handle
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function addMessageListener(string $type, $handle): static
    {
        $msgType = $this->message->MsgType ?: $this->message->msgtype;

        if ($msgType === $type) {
            $this->withHandler($handle);
        }

        return $this;
    }

    /**
     * @param string $event
     * @param        $handle
     *
     * @return $this
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function addEventListener(string $event, $handle): static
    {
        if ($this->message->Event === $event) {
            $this->withHandler($handle);
        }

        return $this;
    }

    /**
     * @param        $response
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function buildResponse($response): mixed
    {
        if (
            empty($response)
            ||
            ServerResponse::SUCCESS_EMPTY_RESPONSE === $response
        ) {
            return ServerResponse::SUCCESS_EMPTY_RESPONSE;
        }

        if ($response instanceof RawMessage) {
            return
                $response->get(
                    'content',
                    ServerResponse::SUCCESS_EMPTY_RESPONSE
                );
        }

        if (is_string($response) || is_numeric($response)) {
            $response = new Text((string) $response);
        }

        if (
            is_array($response)
            &&
            reset($response) instanceof NewsItem
        ) {
            $response = new News($response);
        }

        if (!($response instanceof Message)) {
            throw new InvalidArgumentException(
                sprintf('Invalid Messages type "%s".', gettype($response))
            );
        }

        return $response;
    }

    /**
     * @param string                              $to
     * @param string                              $from
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function buildReply(
        string $to,
        string $from,
        MessageInterface $message
    ): string
    {
        $response = $message->transformToXml([
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => $message->getType(),
        ]);

        if ($this->isSafeMode()) {
            $this->app['logger']->debug('Messages safe mode is enabled.');

            $response = $this->app['encryptor']->encrypt($response);
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->app['config']['token'] ?? '';
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function signature(array $params): string
    {
        sort($params, SORT_STRING);

        return sha1(implode($params));
    }

    /**
     * @param string                            $encrypt
     * @param \EasyWeChat\Kernel\Encryptor|null $encryptor
     *
     * @return string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function decrypt(string $encrypt, Encryptor $encryptor = null): string
    {
        if (!$encryptor) {
            $encryptor = $this->app['encryptor'];
        }

        return
            $encryptor->decrypt(
                $encrypt,
                $this->request->get('msg_signature'),
                $this->request->get('nonce'),
                $this->request->get('timestamp')
            );
    }

    /**
     * @return bool
     */
    public function isSafeMode(): bool
    {
        return $this->request->isSafeMode();
    }
}
