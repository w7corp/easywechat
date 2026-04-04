<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Support\Str;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use EasyWeChat\Pay\Exceptions\SignatureFailureException;
use Nyholm\Psr7\Uri;
use Stringable;

use function array_merge;
use function base64_encode;
use function http_build_query;
use function is_scalar;
use function ltrim;
use function openssl_pkey_get_private;
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
     * @throws SignatureFailureException
     */
    public function createHeader(string $method, string $url, array $options): string
    {
        $uri = new Uri($url);

        parse_str($uri->getQuery(), $query);
        $uri = $uri->withQuery(http_build_query(array_merge($query, (array) ($options['query'] ?? []))));

        $body = '';
        $query = $uri->getQuery();
        $timestamp = time();
        $nonce = Str::random();
        $path = '/'.ltrim($uri->getPath().(empty($query) ? '' : '?'.$query), '/');

        if (! empty($options['body']) && (is_scalar($options['body']) || $options['body'] instanceof Stringable)) {
            $body = strval($options['body']);
        }

        $message = strtoupper($method)."\n".
            $path."\n".
            $timestamp."\n".
            $nonce."\n".
            $body."\n";
        $signature = $this->sign($message);

        return sprintf(
            'WECHATPAY2-SHA256-RSA2048 %s',
            sprintf(
                'mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
                $this->merchant->getMerchantId(),
                $nonce,
                $timestamp,
                $this->merchant->getCertificate()->getSerialNo(),
                $signature
            )
        );
    }

    /**
     * @throws SignatureFailureException
     */
    public function sign(string $message): string
    {
        $privateKey = openssl_pkey_get_private(
            $this->merchant->getPrivateKey()->getKey(),
            $this->merchant->getPrivateKey()->getPassphrase()
        );

        if ($privateKey === false || ! openssl_sign($message, $signature, $privateKey, 'sha256WithRSAEncryption')) {
            throw new SignatureFailureException('Sign failed.');
        }

        return base64_encode($signature);
    }
}
