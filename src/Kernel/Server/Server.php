<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Server;

use EasyWeChat\Kernel\Contracts\MessageInterface;
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

class Server
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

        $this->request = ServerRequest::create($this->app);
        $this->message = $this->request->getMessage();
        $this->withHandlers(
            [
                MessageValidationHandler::class,
            ]
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException|\Throwable
     */
    public function process(): Response
    {
        $response = $this->handle($this->message);

        if ($this->shouldReturnRawResponse()) {
            return new Response($response);
        }

        return
            new Response(
                $this->buildResponse(
                    $this->message->to,
                    $this->message->from,
                    $response
                ),
                200,
                ['Content-Type' => 'application/xml']
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
     * @return \EasyWeChat\Kernel\Server\Server
     *
     * @throws \Throwable
     */
    public function withoutMessageValidationHandler(): Server
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
        if ($this->message->MsgType === $type) {
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
     * @param string $to
     * @param string $from
     * @param        $response
     *
     * @return array|mixed|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function buildResponse(string $to, string $from, $response)
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

        return $this->buildReply($to, $from, $response);
    }

    /**
     * @param string                              $to
     * @param string                              $from
     * @param \EasyWeChat\Kernel\Messages\Message $message
     *
     * @return array|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function buildReply(
        string $to,
        string $from,
        MessageInterface $message
    ): array|string
    {
        $response = $message->transformToXml([
            'ToUserName' => $to,
            'FromUserName' => $from,
            'CreateTime' => time(),
            'MsgType' => $message->getType(),
        ]);

        if ($this->request->isSafeMode()) {
            $this->app['logger']->debug('Messages safe mode is enabled.');

            $response = $this->app['encryptor']->encrypt($response);
        }

        return $response;
    }

    /**
     * @param \EasyWeChat\Kernel\ServiceContainer $app
     *
     * @return string
     */
    public static function getToken(ServiceContainer $app): string
    {
        return $app['config']['token'] ?? '';
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
}
