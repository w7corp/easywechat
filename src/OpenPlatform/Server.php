<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Message;
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

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected AccountInterface $account,
        protected ServerRequestInterface $request,
        protected ?Encryptor $encryptor = null,
    ) {
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

        $message = \EasyWeChat\OpenPlatform\Message::createFromRequest($this->request);
        $query = $this->request->getQueryParams();

        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($query) {
            $this->decryptMessage($message, $this->encryptor, $query['msg_signature'], $query['timestamp'], $query['nonce']);

            return $next($message);
        });

        $response = $this->handle(new Response(200, [], 'SUCCESS'), $message);

        if ($response instanceof ResponseInterface) {
            $response->getBody()->rewind();
            return $response;
        }

        return $this->transformToReply($response, $message, $this->encryptor);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleAuthorized(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'authorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleUnauthorized(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'unauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleAuthorizeUpdated(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'updateauthorized' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleVerifyTicketRefreshed(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'component_verify_ticket' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function resolveResponse(mixed $response, Message $message): ResponseInterface
    {
        return new Response(200, [], 'success');
    }
}
