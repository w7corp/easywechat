<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use Closure;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Exceptions\BadRequestException;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\DecryptMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function func_get_args;

class Server implements ServerInterface
{
    use DecryptMessage;
    use InteractWithHandlers;
    use InteractWithServerRequest;
    use RespondXmlMessage;

    protected ?Closure $defaultVerifyTicketHandler = null;

    public function __construct(
        protected Encryptor $encryptor,
        ?ServerRequestInterface $request = null,
    ) {
        $this->request = $request;
    }

    /**
     * @throws BadRequestException
     */
    public function serve(): ResponseInterface
    {
        $query = $this->getRequest()->getQueryParams();

        if ($echostr = $this->getQueryValue($query, 'echostr')) {
            $this->validatePlainSignature(
                token: $this->encryptor->getToken(),
                signature: $this->getQueryValue($query, 'signature'),
                timestamp: $this->getQueryValue($query, 'timestamp'),
                nonce: $this->getQueryValue($query, 'nonce')
            );

            return new Response(200, ['Content-Type' => 'text/plain'], $echostr);
        }

        $message = $this->getRequestMessage($this->getRequest());

        $this->prepend($this->decryptRequestMessage());

        $response = $this->handle(new Response(200, [], 'success'), $message);

        if (! ($response instanceof ResponseInterface)) {
            $response = $this->transformToReply($response, $message, $this->encryptor);
        }

        return ServerResponse::make($response);
    }

    public function handleAuthorized(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->InfoType === 'authorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUnauthorized(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->InfoType === 'unauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleAuthorizeUpdated(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->InfoType === 'updateauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function withDefaultVerifyTicketHandler(callable $handler): void
    {
        $this->defaultVerifyTicketHandler = fn (): mixed => $handler(...func_get_args());
        $this->handleVerifyTicketRefreshed($this->defaultVerifyTicketHandler);
    }

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
        $query = $this->getRequest()->getQueryParams();

        return function (Message $message, Closure $next) use ($query): mixed {
            $message = $this->decryptMessage(
                message: $message,
                encryptor: $this->encryptor,
                signature: $this->getQueryValue($query, 'msg_signature'),
                timestamp: $this->getQueryValue($query, 'timestamp'),
                nonce: $this->getQueryValue($query, 'nonce')
            );

            return $next($message);
        };
    }

    public function getRequestMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        return Message::createFromRequest($request ?? $this->getRequest());
    }

    public function getDecryptedMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message
    {
        $request = $request ?? $this->getRequest();
        $message = $this->getRequestMessage($request);
        $query = $request->getQueryParams();

        return $this->decryptMessage(
            message: $message,
            encryptor: $this->encryptor,
            signature: $this->getQueryValue($query, 'msg_signature'),
            timestamp: $this->getQueryValue($query, 'timestamp'),
            nonce: $this->getQueryValue($query, 'nonce')
        );
    }
}
