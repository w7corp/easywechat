<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use function base64_decode;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use const OPENSSL_ALGO_SHA256;
use Psr\Http\Message\ResponseInterface;
use function strval;

class ResponseValidator implements \EasyWeChat\Pay\Contracts\ResponseValidator
{
    public const  MAX_ALLOWED_CLOCK_OFFSET = 300;

    public const  HEADER_TIMESTAMP = 'Wechatpay-Timestamp';

    public const  HEADER_NONCE = 'Wechatpay-Nonce';

    public const  HEADER_SERIAL = 'Wechatpay-Serial';

    public const  HEADER_SIGNATURE = 'Wechatpay-Signature';

    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function validate(ResponseInterface $response): void
    {
        if ($response->getStatusCode() !== 200) {
            throw new BadResponseException('Request Failed');
        }

        foreach ([self::HEADER_SIGNATURE, self::HEADER_TIMESTAMP, self::HEADER_SERIAL, self::HEADER_NONCE] as $header) {
            if (! $response->hasHeader($header)) {
                throw new BadResponseException("Missing Header: {$header}");
            }
        }

        [$timestamp] = $response->getHeader(self::HEADER_TIMESTAMP);
        [$nonce] = $response->getHeader(self::HEADER_NONCE);
        [$serial] = $response->getHeader(self::HEADER_SERIAL);
        [$signature] = $response->getHeader(self::HEADER_SIGNATURE);

        $body = (string) $response->getBody();

        $message = "{$timestamp}\n{$nonce}\n{$body}\n";

        if (\time() - \intval($timestamp) > self::MAX_ALLOWED_CLOCK_OFFSET) {
            throw new BadResponseException('Clock Offset Exceeded');
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
            throw new BadResponseException('Invalid Signature');
        }
    }
}
