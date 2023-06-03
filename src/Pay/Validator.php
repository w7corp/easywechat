<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use EasyWeChat\Pay\Exceptions\InvalidSignatureException;
use Psr\Http\Message\MessageInterface;

class Validator implements \EasyWeChat\Pay\Contracts\Validator
{
    public const MAX_ALLOWED_CLOCK_OFFSET = 300;

    public const HEADER_TIMESTAMP = 'Wechatpay-Timestamp';

    public const HEADER_NONCE = 'Wechatpay-Nonce';

    public const HEADER_SERIAL = 'Wechatpay-Serial';

    public const HEADER_SIGNATURE = 'Wechatpay-Signature';

    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Pay\Exceptions\InvalidSignatureException
     */
    public function validate(MessageInterface $message): void
    {
        foreach ([self::HEADER_SIGNATURE, self::HEADER_TIMESTAMP, self::HEADER_SERIAL, self::HEADER_NONCE] as $header) {
            if (! $message->hasHeader($header)) {
                throw new InvalidSignatureException("Missing Header: {$header}");
            }
        }

        [$timestamp] = $message->getHeader(self::HEADER_TIMESTAMP);
        [$nonce] = $message->getHeader(self::HEADER_NONCE);
        [$serial] = $message->getHeader(self::HEADER_SERIAL);
        [$signature] = $message->getHeader(self::HEADER_SIGNATURE);

        $body = (string) $message->getBody();

        $message = "{$timestamp}\n{$nonce}\n{$body}\n";

        if (\time() - \intval($timestamp) > self::MAX_ALLOWED_CLOCK_OFFSET) {
            throw new InvalidSignatureException('Clock Offset Exceeded');
        }

        $publicKey = $this->merchant->getPlatformCert($serial);

        if (! $publicKey) {
            throw new InvalidConfigException(
                "No platform certs found for serial: {$serial}, 
                please download from wechat pay and set it in merchant config with key `certs`."
            );
        }

        if (false === \openssl_verify(
            $message,
            base64_decode($signature),
            strval($publicKey),
            OPENSSL_ALGO_SHA256
        )) {
            throw new InvalidSignatureException('Invalid Signature');
        }
    }
}
