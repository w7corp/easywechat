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
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012791861
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013070368
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012791836
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012791882
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012791902
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012158598
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421143
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421231
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421336
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421407
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012587960
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012647435
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085146
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013080237
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085680
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085875
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085801
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012231898
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462105
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462175
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462250
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462574
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012155283
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012586136
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085573
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013194298
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012284311
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012285856
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012286313
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012595808
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012289459
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013392770
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012086059
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012090195
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012076414
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012159706
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011935221
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011937152
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011937248
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011938508
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011941607
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011985057
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011985480
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011936650
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011989262
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011941679
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011941306
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011984334
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011989906
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011988207
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011984263
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
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012791865
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013070388
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012810605
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012791886
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012791906
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012085921
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421172
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421273
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421356
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013421448
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4013071196
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012268885
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012082022
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012587976
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012647469
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012083103
     * @link https://pay.weixin.qq.com/doc/v3/merchant/4012285869
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085298
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013080241
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085681
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085876
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012085802
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012231901
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462126
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462195
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462327
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013462586
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013080628
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012167494
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012650317
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012166857
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012886275
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012086319
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012124635
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012076419
     * @link https://pay.weixin.qq.com/doc/v3/partner/4013163616
     * @link https://pay.weixin.qq.com/doc/v3/partner/4012586138
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011940955
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011935223
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011937201
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011939959
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011939475
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011941681
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011985425
     * @link https://pay.weixin.qq.com/doc/v2/merchant/4011987569
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011941745
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011936652
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011989265
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011984153
     * @link https://pay.weixin.qq.com/doc/v2/partner/4012297550
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011984440
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011989912
     * @link https://pay.weixin.qq.com/doc/v2/partner/4011988218
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
     * @throws InvalidArgumentException
     * @throws RuntimeException
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
     * @throws RuntimeException
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

    public function getDecryptedMessage(?ServerRequestInterface $request = null): \EasyWeChat\Kernel\Message|Message
    {
        return $this->getRequestMessage($request);
    }
}
