<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Psr\Http\Message\RequestInterface;

class Signature
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    public function createHeader(RequestInterface $request): string
    {
        $body = '';
        $nonce = \uniqid('nonce');
        $timestamp = \time();
        $query = $request->getUri()->getQuery();
        $path = '/' . \ltrim($request->getUri()->getPath() .(empty($query) ? '' : '?' . $query), '/');

        if ($request->getBody()->isSeekable()) {
            $body = \strval($request->getBody());
            $request->getBody()->rewind();
        }

        $message = $request->getMethod() . "\n" .
            $path . "\n" .
            $timestamp . "\n" .
            $nonce . "\n" .
            $body . "\n";

        \openssl_sign($message, $signature, $this->merchant->getPrivateKey(), 'sha256WithRSAEncryption');

        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 %s',
            sprintf(
                'mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
                $this->merchant->getMerchantId(),
                $nonce,
                $timestamp,
                $this->merchant->getCertificateSerialNumber(),
                \base64_encode($signature)
            )
        );
    }
}
