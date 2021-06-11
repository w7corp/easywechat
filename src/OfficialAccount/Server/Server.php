<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Traits\Observable;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\Server\Handlers\MessageValidationHandler;
use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use EasyWeChat\OfficialAccount\Contracts\Server as ServerInterface;
use EasyWeChat\OfficialAccount\Contracts\Request as RequestInterface;
use EasyWeChat\OfficialAccount\Contracts\Response as ResponseInterface;
use EasyWeChat\OfficialAccount\Server\Handlers\ServerValidationHandler;
use function EasyWeChat\Kernel\throw_if;

class Server implements ServerInterface
{
    use Observable;

    protected AccountInterface $account;
    protected RequestInterface $request;

    // 消息类型
    public const TEXT = 'text';
    public const IMAGE = 'image';
    public const VOICE = 'voice';
    public const VIDEO = 'video';
    public const SHORT_VIDEO = 'shortvideo';
    public const LOCATION = 'location';
    public const LINK = 'link';
    public const DEVICE_EVENT = 'device_event';
    public const DEVICE_TEXT = 'device_text';
    public const EVENT = 'event';
    public const DEVICE_FILE = 'file';
    public const DEVICE_MINIPROGRAM_PAGE = 'miniprogrampage';

    // 事件类型
    public const SUBSCRIBE_EVENT = 'subscribe';
    public const UNSUBSCRIBE_EVENT = 'unsubscribe';
    public const SCAN_EVENT = 'SCAN';
    public const LOCATION_EVENT = 'LOCATION';
    public const CLICK_EVENT = 'CLICK';
    public const VIEW_EVENT = 'VIEW';

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function __construct(
        protected Application $application,
    ) {
        $this->account = $this->application->getAccount();
        $this->request = $this->application->getRequest();

        $this->withHandlers([
            MessageValidationHandler::class,
            ServerValidationHandler::class,
        ]);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function process(): ResponseInterface
    {
        $message = $this->request->getMessage($this->application->getEncryptor());

        $response = $this->handle($message);

        if (
            $this->request->isValidation()
            ||
            empty($response)
            ||
            Response::SUCCESS_EMPTY_RESPONSE === $response
        ) {
            return Response::success($response);
        }

        $response = $this->buildResponse($response);

        $currentTime = \time();

        return
            Response::replay(
                \array_merge(
                    [
                        'ToUserName' => $message->to,
                        'FromUserName' => $message->from,
                        'CreateTime' => $currentTime,
                    ],
                    $response
                ),
                $this->application,
                [
                    'time' => $currentTime,
                ]
            );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function buildResponse($response): array
    {
        if (\is_array($response)) {
            throw_if(
                !isset($response['MsgType']),
                InvalidArgumentException::class,
                'MsgType cannot be empty.'
            );

            return $response;
        }

        if (is_string($response) || is_numeric($response)) {
            return [
                'MsgType' => self::TEXT,
                'Content' => $response,
            ];
        }

        throw new InvalidArgumentException(
            sprintf('Invalid Response type "%s".', gettype($response))
        );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function withoutMessageValidationHandler(): static
    {
        return $this->withoutHandler(MessageValidationHandler::class);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function addMessageListener(string $type, $handler): static
    {
        $this->withHandler(fn ($message) => $message->MsgType === $type ? $handler($message) : null);

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function addEventListener(string $event, $handler): static
    {
        $this->withHandler(fn ($message) => $message->Event === $event ? $handler($message) : null);

        return $this;
    }
}
