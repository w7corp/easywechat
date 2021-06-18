<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithXmlMessage;
use EasyWeChat\OpenPlatform\Contracts\Account as AccountInterface;
use EasyWeChat\OpenPlatform\Contracts\Server as ServerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;
    use InteractWithXmlMessage;

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
     */
    public function handleAuthorized(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return $message->InfoType === 'authorized' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleUnauthorized(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return $message->InfoType === 'unauthorized' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleAuthorizeUpdated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return $message->InfoType === 'updateauthorized' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleVerifyTicketRefreshed(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return $message->InfoType === 'component_verify_ticket' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }
}
