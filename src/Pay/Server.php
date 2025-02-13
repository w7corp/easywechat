<?php

namespace EasyWeChat\Pay;

use Closure;
use EasyWeChat\Kernel\Contracts\Server as ServerInterface;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\ServerResponse;
use EasyWeChat\Kernel\Support\AesEcb;
use EasyWeChat\Kernel\Support\AesGcm;
use EasyWeChat\Kernel\Support\Xml;
use EasyWeChat\Kernel\Traits\InteractWithHandlers;
use EasyWeChat\Kernel\Traits\InteractWithServerRequest;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Exception;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_key_exists;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use function str_contains;
use function strval;

/**
 * @link https://pay.weixin.qq.com/wiki/doc/apiv3/wechatpay/wechatpay4_1.shtml
 * @link https://pay.weixin.qq.com/wiki/doc/apiv3/apis/chapter3_1_5.shtml
 */
class Server implements ServerInterface
{
    use InteractWithHandlers;
    use InteractWithServerRequest;

    public function __construct(
        protected MerchantInterface $merchant,
        ?ServerRequestInterface $request,
    ) {
        $this->request = $request;
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
        $originContent = (string) ($request ?? $this->getRequest())->getBody();

        // 微信支付的回调数据回调，偶尔是 XML https://github.com/w7corp/easywechat/issues/2737
        $contentType = ($request ?? $this->getRequest())->getHeaderLine('content-type');
        $isXml = (str_contains($contentType, 'text/xml') || str_contains($contentType, 'application/xml')) && str_starts_with($originContent, '<xml');
        $attributes = $isXml ? $this->decodeXmlMessage($originContent) : $this->decodeJsonMessage($originContent);

        return new Message($attributes, $originContent);
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function decodeXmlMessage(string $contents): array
    {
        $attributes = Xml::parse($contents);

        if (! is_array($attributes)) {
            throw new RuntimeException('Invalid request body.');
        }

        if (! empty($attributes['req_info'])) {
            $key = $this->merchant->getV2SecretKey();

            if (empty($key)) {
                throw new InvalidArgumentException('V2 secret key is required.');
            }

            $attributes = Xml::parse(AesEcb::decrypt($attributes['req_info'], md5($key), iv: ''));
        }

        if (
            is_array($attributes)
            && array_key_exists('event_ciphertext', $attributes) && is_string($attributes['event_ciphertext'])
            && array_key_exists('event_nonce', $attributes) && is_string($attributes['event_nonce'])
            && array_key_exists('event_associated_data', $attributes) && is_string($attributes['event_associated_data'])
        ) {
            $attributes += Xml::parse(AesGcm::decrypt(
                $attributes['event_ciphertext'],
                $this->merchant->getSecretKey(),
                $attributes['event_nonce'],
                $attributes['event_associated_data'] // maybe empty string
            ));
        }

        if (! is_array($attributes)) {
            throw new RuntimeException('Failed to decrypt request message.');
        }

        return $attributes;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     */
    protected function decodeJsonMessage(string $contents): array
    {
        $attributes = json_decode($contents, true);

        if (! (is_array($attributes) && is_array($attributes['resource']))) {
            throw new RuntimeException('Invalid request body.');
        }

        if (empty($attributes['resource']['ciphertext'] ?? null)) {
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

        return $attributes;
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
