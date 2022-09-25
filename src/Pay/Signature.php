<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use function base64_encode;
use EasyWeChat\Kernel\Support\Str;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Exception;
use function http_build_query;
use function ltrim;
use Nyholm\Psr7\Uri;
use function openssl_sign;
use function parse_str;
use function strtoupper;
use function strval;
use function time;

class Signature
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @param  array<string,mixed>  $options
     *
     * @throws Exception
     */
    public function createHeader(string $method, string $url, array $options): string
    {
        $uri = new Uri($url);

        parse_str($uri->getQuery(), $query);
        $uri = $uri->withQuery(http_build_query(array_merge($query, (array) $options['query'])));

        $body = '';
        $query = $uri->getQuery();
        $timestamp = time();
        $nonce = Str::random();
        $path = '/'.ltrim($uri->getPath().(empty($query) ? '' : '?'.$query), '/');

        if (! empty($options['body'])) {
            $body = strval($options['body']);
        }

        $message = strtoupper($method)."\n".
            $path."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";

        openssl_sign($message, $signature, $this->merchant->getPrivateKey()->getKey(), 'sha256WithRSAEncryption');

        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 %s',
            sprintf(
                'mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
                $this->merchant->getMerchantId(),
                $nonce,
                $timestamp,
                $this->merchant->getCertificate()->getSerialNo(),
                base64_encode($signature)
            )
        );
    }
}
