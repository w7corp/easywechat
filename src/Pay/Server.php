<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Server implements ServerInterface
{
    use InteractWithHandlers;

    /**
     * @throws \Throwable
     */
    public function __construct(
        protected MerchantInterface $merchant,
        protected ServerRequestInterface $request,
    ) {
    }

    public function serve(): ResponseInterface
    {
        $message = $this->createMessageFromRequest();

        try {
            return $this->handle(new Response(200, [], 'SUCCESS'), $message);
        } catch (\Exception $e) {
            return new Response(500, [], $e->getMessage());
        }
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handlePaid(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return $message->event_type === 'TRANSACTION.SUCCESS' ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleRefunded(callable | string $handler): static
    {
        $this->with(function (\EasyWeChat\Kernel\Message $message, \Closure $next) use ($handler): mixed {
            return in_array($message->event_type, [
                'REFUND.SUCCESS',
                'REFUND.ABNORMAL',
                'REFUND.CLOSED',
            ]) ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    protected function createMessageFromRequest(): Message
    {
        $originContent = $this->request->getBody()->getContents();
        $attributes = \json_decode($originContent, true);

        //todo: decode

        return new Message($attributes, $originContent);
    }
}
