<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use Closure;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\DecryptMessage;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Kernel\Traits\RespondJsonMessage;
use EasyWeChat\Kernel\Traits\RespondXmlMessage;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use DecryptMessage;
    use InteractWithHandlers;
    use InteractWithServerRequest;
    use RespondJsonMessage;
    use RespondXmlMessage;

    public function __construct(
        protected Encryptor $encryptor,
        ?ServerRequestInterface $request = null,
        protected string $messageType = 'xml',
    ) {
        $this->request = $request;
    }

    public function serve(): ResponseInterface
    {
        $query = $this->getRequest()->getQueryParams();

        if (! empty($query['echostr'])) {
            $response = $this->encryptor->decrypt(
                $query['echostr'],
                $query['msg_signature'] ?? '',
                $query['nonce'] ?? '',
                $query['timestamp'] ?? ''
            );

            return new Response(200, [], $response);
        }

        $message = $this->getRequestMessage($this->getRequest());

        $this->prepend($this->decryptRequestMessage());

        $response = $this->handle(new Response(200, [], 'SUCCESS'), $message);

        if (! ($response instanceof ResponseInterface)) {
            $response = $this->messageType === 'xml' ?
                $this->transformToReply($response, $message, $this->encryptor) :
                $this->transformJsonToReply($response, $message, $this->encryptor);
        }

        return ServerResponse::make($response);
    }

    public function handleContactChanged(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserTagUpdated(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'update_tag' ? $handler(
                $message,
                $next
            ) : $next($message);
        });

        return $this;
    }

    public function handleUserCreated(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'create_user' ? $handler(
                $message,
                $next
            ) : $next($message);
        });

        return $this;
    }

    public function handleUserUpdated(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'update_user' ? $handler(
                $message,
                $next
            ) : $next($message);
        });

        return $this;
    }

    public function handleUserDeleted(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'delete_user' ? $handler(
                $message,
                $next
            ) : $next($message);
        });

        return $this;
    }

    public function handlePartyCreated(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'create_party' ? $handler(
                $message,
                $next
            ) : $next($message);
        });

        return $this;
    }

    public function handlePartyUpdated(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'update_party' ? $handler(
                $message,
                $next
            ) : $next($message);
        });

        return $this;
    }

    public function handlePartyDeleted(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'delete_party' ? $handler(
                $message,
                $next
            ) : $next($message);
        });

        return $this;
    }

    public function handleBatchJobsFinished(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->Event === 'batch_job_result' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function addMessageListener(string $type, callable $handler): static
    {
        $this->withHandler(
            function (Message $message, Closure $next) use ($type, $handler): mixed {
                return $message->MsgType === $type ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    public function addEventListener(string $event, callable $handler): static
    {
        $this->withHandler(
            function (Message $message, Closure $next) use ($event, $handler): mixed {
                return $message->Event === $event ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    protected function validateUrl(): Closure
    {
        return function (Message $message, Closure $next): Response {
            $query = $this->getRequest()->getQueryParams();
            $response = $this->encryptor->decrypt(
                $query['echostr'],
                $query['msg_signature'] ?? '',
                $query['nonce'] ?? '',
                $query['timestamp'] ?? ''
            );

            return new Response(200, [], $response);
        };
    }

    protected function decryptRequestMessage(): Closure
    {
        return function (Message $message, Closure $next): mixed {
            $query = $this->getRequest()->getQueryParams();

            $params = [
                $query['msg_signature'] ?? '',
                $query['timestamp'] ?? '',
                $query['nonce'] ?? '',
            ];

            $this->decryptMessage($message, $this->encryptor, ...$params);

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

        $params = [
            $query['msg_signature'] ?? '',
            $query['timestamp'] ?? '',
            $query['nonce'] ?? '',
        ];

        return $this->decryptMessage(
            $message,
            $this->encryptor,
            ...$params
        );
    }
}
