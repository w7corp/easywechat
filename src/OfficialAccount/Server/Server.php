<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount\Server;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\OfficialAccount\Server\Handlers\MessageValidationHandler;
use EasyWeChat\OfficialAccount\Server\Handlers\ServerValidationHandler;
use EasyWeChat\Kernel\Traits\Observable;
use EasyWeChat\OfficialAccount\Contracts\Account;
use Symfony\Component\HttpFoundation\Response;
use EasyWeChat\Kernel\Server\Response as ServerResponse;

class Server
{
    use Observable;

    public Request $request;

    public const SUCCESS_EMPTY_RESPONSE = 'success';

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function __construct(
        protected Account $account,
        ?Request $request = null,
        protected ?Encryptor $encryptor = null
    ) {
        $this->request = $request ?? Request::create($this);

        $this->withHandlers(
            handlers: [
                          MessageValidationHandler::class,
                          ServerValidationHandler::class,
                      ]
        );
    }

    public function process(): Response | ServerResponse
    {
        $message = $this->request->getMessage();
        $response = $this->handle($message->toArray());

        if ($this->shouldReturnRawResponse()) {
            return new Response($response);
        }

        return
            new Response(
                $this->buildResponse(
                    $message->to,
                    $message->from,
                    $response
                ),
                200,
                ['Content-Type' => 'application/xml']
            );
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function buildResponse(string $to, string $from, array | string | null $message): array | string
    {
        if (empty($message) || self::SUCCESS_EMPTY_RESPONSE === $message) {
            return self::SUCCESS_EMPTY_RESPONSE;
        }

        $message = \array_merge(
            [
                'ToUserName' => $to,
                'FromUserName' => $from,
                'CreateTime' => time(),
            ],
            $message
        );

        if ($this->request->isSafeMode()) {
            $message = $this->encryptor->encrypt($message);
        }

        return $message;
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
