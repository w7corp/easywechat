<?php

namespace EasyWeChat\Pay;

use Closure;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Support\AesGcm;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Exception;
use function is_array;
use function json_decode;
use function json_encode;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function strval;
use Throwable;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay4_1.shtml
 * @link https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml
 */
class Server implements ServerInterface
{
    use InteractWithHandlers;

    protected ServerRequestInterface $request;

    /**
     * @throws Throwable
     */
    public function __construct(
        protected MerchantInterface $merchant,
        ?ServerRequestInterface $request,
    ) {
        $this->request = $request ?? RequestUtil::createDefaultServerRequest();
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function serve(): ResponseInterface
    {
        $message = $this->getRequestMessage();

        try {
            $defaultResponse = new Response(
                200,
                [],
                strval(json_encode(['code' => 'SUCCESS', 'message' => '成功'], JSON_UNESCAPED_UNICODE))
            );
            $response = $this->handle($defaultResponse, $message);

            if (! ($response instanceof ResponseInterface)) {
                $response = $defaultResponse;
            }

            return ServerResponse::make($response);
        } catch (Exception $e) {
            return new Response(
                500,
                [],
                strval(json_encode(['code' => 'ERROR', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE))
            );
        }
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml
     *
     * @throws InvalidArgumentException
     */
    public function handlePaid(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return $message->getEventType() === 'TRANSACTION.SUCCESS' && $message->trade_state === 'SUCCESS'
                ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @link https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_11.shtml
     *
     * @throws InvalidArgumentException
     */
    public function handleRefunded(callable $handler): static
    {
        $this->with(function (Message $message, Closure $next) use ($handler): mixed {
            return in_array($message->getEventType(), [
                'REFUND.SUCCESS',
                'REFUND.ABNORMAL',
                'REFUND.CLOSED',
            ]) ? $handler($message, $next) : $next($message);
        });

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function getRequestMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message|Message
    {
        $originContent = (string) ($request ?? $this->request)->getBody();
        $attributes = json_decode($originContent, true);

        if (! is_array($attributes)) {
            throw new RuntimeException('Invalid request body.');
        }

        if (empty($attributes['resource']['ciphertext'])) {
            throw new RuntimeException('Invalid request.');
        }

        $attributes = json_decode(
            AesGcm::decrypt(
                $attributes['resource']['ciphertext'],
                $this->merchant->getSecretKey(),
                $attributes['resource']['nonce'],
                $attributes['resource']['associated_data'],
            ),
            true
        );

        if (! is_array($attributes)) {
            throw new RuntimeException('Failed to decrypt request message.');
        }

        return new Message($attributes, $originContent);
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function getDecryptedMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message|Message
    {
        return $this->getRequestMessage($request);
    }
}
