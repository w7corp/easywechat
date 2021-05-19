<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseValidator implements \EasyWeChat\Pay\Contracts\ResponseValidator
{
    public function __construct(protected MerchantInterface $merchant)
    {
    }

    public function validate(ResponseInterface $response): bool
    {
        //todo
    }
}
