<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Support\AesGcm;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay4_1.shtml
 */
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

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    public function serve(): ResponseInterface
    {
        $message = $this->createMessageFromRequest();

        try {
            return $this->handle(
                new Response(200, [], \strval(\json_encode(['code' => 'SUCCESS', 'message' => '成功'], JSON_UNESCAPED_UNICODE))),
                $message
            );
        } catch (\Exception $e) {
            return new Response(
                500,
                [],
                \strval(\json_encode(['code' => 'ERROR', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE))
            );
        }
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handlePaid(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return $message->getEventType() === 'TRANSACTION.SUCCESS' && $message->trade_state === 'SUCCESS'
                ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     */
    public function handleRefunded(callable $handler): static
    {
        $this->with(function (Message $message, \Closure $next) use ($handler): mixed {
            return in_array($message->getEventType(), [
                'REFUND.SUCCESS',
                'REFUND.ABNORMAL',
                'REFUND.CLOSED',
            ]) ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function createMessageFromRequest(): Message
    {
        $originContent = $this->request->getBody()->getContents();
        $attributes = \json_decode($originContent, true);

        if (empty($attributes['resource']['ciphertext'])) {
            throw new RuntimeException('Invalid request.');
        }

        $attributes = \json_decode(
            AesGcm::decrypt(
                $attributes['resource']['ciphertext'],
                $this->merchant->getSecretKey(),
                $attributes['resource']['nonce'],
                $attributes['resource']['associated_data'],
            ),
            true
        );

        return new Message($attributes, $originContent);
    }
}
