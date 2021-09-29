<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithXmlMessage;
use EasyWeChat\Work\Contracts\Account as AccountInterface;
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

    // 成员变更通知 + 部门变更通知 + 标签变更通知
    public function handleContactChanged(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserTagUpdated(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'update_tag' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserCreated(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'create_user' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserUpdated(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'update_user' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleUserDeleted(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->Event === 'change_contact' && $message->ChangeType === 'delete_user' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handlePartyCreated(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'create_party' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handlePartyUpdated(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'update_party' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handlePartyDeleted(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->InfoType === 'change_contact' && $message->ChangeType === 'delete_party' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    public function handleBatchJobsFinished(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->Event === 'batch_job_result' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function addMessageListener(string $type, callable | string $handler): static
    {
        $this->withHandler(
            function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($type, $handler): mixed {
                return $message->MsgType === $type ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }

    /**
     * @throws \Throwable
     */
    public function addEventListener(string $event, callable | string $handler): static
    {
        $this->withHandler(
            function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($event, $handler): mixed {
                return $message->Event === $event ? $handler($message, $next) : $next($message);
            }
        );

        return $this;
    }
}
