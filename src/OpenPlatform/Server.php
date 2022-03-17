<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\DecryptXmlMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use EasyWeChat\OpenPlatform\Contracts\Account as AccountInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;
    use RespondXmlMessage;
    use DecryptXmlMessage;

    protected ?\Closure $defaultVerifyTicketHandler = null;
    protected ServerRequestInterface $request;

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected AccountInterface $account,
        protected Encryptor $encryptor,
        ?ServerRequestInterface $request = null,
    ) {
        $this->request = $request ?? RequestUtil::createDefaultServerRequest();
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function serve(): ResponseInterface
    {
        if (!!($str = $this->request->getQueryParams()['echostr'] ?? '')) {
            return new Response(200, [], $str);
        }

        $message = $this->getRequestMessage($this->request);

        $this->prepend($this->decryptRequestMessage());

        $response = $this->handle(new Response(200, [], 'success'), $message);

        if (!($response instanceof ResponseInterface)) {
            $response = $this->transformToReply($response, $message, $this->encryptor);
        }

        return ServerResponse::make($response);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleAuthorized(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'authorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleUnauthorized(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'unauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleAuthorizeUpdated(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'updateauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function withDefaultVerifyTicketHandler(callable $handler): void
    {
        $this->defaultVerifyTicketHandler = fn (): mixed => $handler(...\func_get_args());
        $this->handleVerifyTicketRefreshed($this->defaultVerifyTicketHandler);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleVerifyTicketRefreshed(callable $handler): static
    {
        if ($this->defaultVerifyTicketHandler) {
            $this->withoutHandler($this->defaultVerifyTicketHandler);
        }

        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'component_verify_ticket' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    protected function decryptRequestMessage(): \Closure
    {
        $query = $this->request->getQueryParams();

        return function (Message $message, \Closure $next) use ($query): mixed {
            $message = $this->decryptMessage(
                $message,
                $this->encryptor,
                $query['msg_signature'] ?? '',
                $query['timestamp'] ?? '',
                $query['nonce'] ?? ''
            );

            return $next($message);
        };
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     */
    public function getRequestMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        return Message::createFromRequest($request ?? $this->request);
    }
}
