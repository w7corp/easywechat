<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use Closure;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\DecryptMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class Server implements ServerInterface
{
    use DecryptMessage;
    use InteractWithHandlers;
    use InteractWithServerRequest;
    use RespondXmlMessage;

    public function __construct(
        ?ServerRequestInterface $request = null,
        protected ?Encryptor $encryptor = null,
    ) {
        $this->request = $request;
    }

    public function serve(): ResponseInterface
    {
        if ($str = $this->getRequest()->getQueryParams()['echostr'] ?? '') {
            return new Response(200, [], $str);
        }

        $message = $this->getRequestMessage($this->getRequest());
        $query = $this->getRequest()->getQueryParams();

        if ($this->encryptor && ! empty($query['msg_signature'])) {
            $this->prepend($this->decryptRequestMessage($query));
        }

        $response = $this->handle(new Response(200, [], 'success'), $message);

        if (! ($response instanceof ResponseInterface)) {
            $response = $this->transformToReply($response, $message, $this->encryptor);
        }

        return ServerResponse::make($response);
    }

    /**
     * @throws Throwable
     */
    public function addMessageListener(string $type, callable|string $handler): static
    {
        $handler = $this->makeClosure($handler);
        $this->withHandler(
            function (Message $message, Closure $next) use ($type, $handler): mixed {
                return $message->MsgType === $type ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    public function addEventListener(string $event, callable|string $handler): static
    {
        $handler = $this->makeClosure($handler);
        $this->withHandler(
            function (Message $message, Closure $next) use ($event, $handler): mixed {
                return $message->Event === $event ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @param  array<string,string>  $query
     *
     * @psalm-suppress PossiblyNullArgument
     */
    protected function decryptRequestMessage(array $query): Closure
    {
        return function (Message $message, Closure $next) use ($query): mixed {
            if (! $this->encryptor) {
                return null;
            }

            $this->decryptIncomingMessage($message, $query);

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

        if (! $this->encryptor || empty($query['msg_signature'])) {
            return $message;
        }

        return $this->decryptIncomingMessage($message, $query);
    }

    /**
     * @param  array<string,string>  $query
     */
    protected function decryptIncomingMessage(\EasyWeChat\Kernel\Message $message, array $query): \EasyWeChat\Kernel\Message
    {
        if (! $this->encryptor) {
            return $message;
        }

        $signature = $query['msg_signature'] ?? '';
        $timestamp = $query['timestamp'] ?? '';
        $nonce = $query['nonce'] ?? '';

        return $this->decryptMessage(
            message: $message,
            encryptor: $this->encryptor,
            signature: $signature,
            timestamp: $timestamp,
            nonce: $nonce
        );
    }
}
