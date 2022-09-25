<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use Closure;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\DecryptXmlMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use function func_get_args;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;
    use RespondXmlMessage;
    use DecryptXmlMessage;

    protected ?Closure $defaultVerifyTicketHandler = null;

    protected ServerRequestInterface $request;

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected Encryptor $encryptor,
        ?ServerRequestInterface $request = null,
    ) {
        $this->request = $request ?? RequestUtil::createDefaultServerRequest();
    }

    /**
     * @throws InvalidArgumentException
     * @throws BadRequestException
     * @throws RuntimeException
     */
    public function serve(): ResponseInterface
    {
        if ((bool) ($str = $this->request->getQueryParams()['echostr'] ?? '')) {
            return new Response(200, [], $str);
        }

        $message = $this->getRequestMessage($this->request);

        $this->prepend($this->decryptRequestMessage());

        $response = $this->handle(new Response(200, [], 'success'), $message);

        if (! ($response instanceof ResponseInterface)) {
            $response = $this->transformToReply($response, $message, $this->encryptor);
        }

        return ServerResponse::make($response);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleAuthorized(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->InfoType === 'authorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleUnauthorized(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->InfoType === 'unauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleAuthorizeUpdated(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->InfoType === 'updateauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function withDefaultVerifyTicketHandler(callable $handler): void
    {
        $this->defaultVerifyTicketHandler = fn (): mixed => $handler(...func_get_args());
        $this->handleVerifyTicketRefreshed($this->defaultVerifyTicketHandler);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handleVerifyTicketRefreshed(callable $handler): static
    {
        if ($this->defaultVerifyTicketHandler) {
            $this->withoutHandler($this->defaultVerifyTicketHandler);
        }

        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->InfoType === 'component_verify_ticket' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    protected function decryptRequestMessage(): Closure
    {
        $query = $this->request->getQueryParams();

        return function (Message $message, Closure $next) use ($query): mixed {
            $message = $this->decryptMessage(
                message: $message,
                encryptor: $this->encryptor,
                signature: $query['msg_signature'] ?? '',
                timestamp: $query['timestamp'] ?? '',
                nonce: $query['nonce'] ?? ''
            );

            return $next($message);
        };
    }

    /**
     * @throws BadRequestException
     */
    public function getRequestMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        return Message::createFromRequest($request ?? $this->request);
    }

    /**
     * @throws BadRequestException
     * @throws RuntimeException
     */
    public function getDecryptedMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        $request = $request ?? $this->request;
        $message = $this->getRequestMessage($request);
        $query = $request->getQueryParams();

        return $this->decryptMessage(
            message: $message,
            encryptor: $this->encryptor,
            signature: $query['msg_signature'] ?? '',
            timestamp: $query['timestamp'] ?? '',
            nonce: $query['nonce'] ?? ''
        );
    }
}
