<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\HttpClient\Response as HttpClientResponse;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Psr\Http\Message\ResponseInterface as PsrResponse;

class ResponseValidator implements \EasyWeChat\Pay\Contracts\ResponseValidator
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\BadResponseException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Pay\Exceptions\InvalidSignatureException
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function validate(PsrResponse|HttpClientResponse $response): void
    {
        if ($response instanceof HttpClientResponse) {
            $response = $response->toPsrResponse();
        }

        if ($response->getStatusCode() !== 200) {
            throw new BadResponseException('Request Failed');
        }

        (new Validator($this->merchant))->validate($response);
    }
}
