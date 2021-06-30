<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Encryptor;
use EasyWeChat\Kernel\Message;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithXmlMessage;
use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use Psr\Http\Message\ResponseInterface;
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
        protected Encryptor $encryptor,
    ) {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleSuiteTicketRefreshed(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return $message->InfoType === 'suite_ticket' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    // 成员变更通知 + 部门变更通知 + 标签变更通知
    public function handleContactChanged(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return $message->Event === 'change_contact' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleUserTagUpdated(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->Event === 'change_contact' && $message->$message->ChangeType === 'update_tag' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function handleBatchJobCompleted(callable | string $handler): static
    {
        $this->with(
            function ($message, \Closure $next) use ($handler) {
                return
                    $message->Event === 'batch_job_result' ? $handler($message) : $next($message);
            }
        );

        return $this;
    }

    public function transformResponse(array $response, Message $message): ResponseInterface
    {
        return ServerResponse::success();
    }
}
