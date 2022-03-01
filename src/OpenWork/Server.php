<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Traits\DecryptXmlMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;
    use RespondXmlMessage;
    use DecryptXmlMessage;

    protected \Closure | null $defaultSuiteTicketHandler = null;

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected AccountInterface $account,
        protected ServerRequestInterface $request,
        protected Encryptor $encryptor,
        protected Encryptor $providerEncryptor
    ) {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function serve(): ResponseInterface
    {
        $query = $this->request->getQueryParams();

        if (!!($str = $query['echostr'] ?? '')) {
            $response = $this->providerEncryptor->decrypt(
                $str,
                $query['msg_signature'] ?? '',
                $query['nonce'] ?? '',
                $query['timestamp'] ?? ''
            );

            return new Response(200, [], $response);
        }

        $message = Message::createFromRequest($this->request);

        $this->prepend($this->decryptRequestMessage());

        $response = $this->handle(new Response(200, [], 'success'), $message);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        return $this->transformToReply($response, $message, $this->encryptor);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function withDefaultSuiteTicketHandler(callable $handler): void
    {
        $this->defaultSuiteTicketHandler = fn (): mixed => $handler(...\func_get_args());
        $this->handleSuiteTicketRefreshed($this->defaultSuiteTicketHandler);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleSuiteTicketRefreshed(callable $handler): static
    {
        if ($this->defaultSuiteTicketHandler) {
            $this->withoutHandler($this->defaultSuiteTicketHandler);
        }

        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'suite_ticket' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleAuthCreated(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'create_auth' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleAuthChanged(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_auth' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleAuthCancelled(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'cancel_auth' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserCreated(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'create_user' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserUpdated(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'update_user' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserDeleted(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'delete_user' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handlePartyCreated(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'create_party' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handlePartyUpdated(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'update_party' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handlePartyDeleted(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'delete_party' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserTagUpdated(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'update_tag' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleShareAgentChanged(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'share_agent_change' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    protected function decryptRequestMessage(): \Closure
    {
        $query = $this->request->getQueryParams();
        return function (Message $message, \Closure $next) use ($query): mixed {
            $this->decryptMessage(
                $message,
                $this->encryptor,
                $query['msg_signature'],
                $query['timestamp'],
                $query['nonce']
            );

            return $next($message);
        };
    }
}
