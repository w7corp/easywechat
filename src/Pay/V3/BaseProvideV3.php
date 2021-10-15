<?php

namespace EasyWeChat\Pay\V3;

use EasyWeChat\Pay\Merchant;
use EasyWeChat\Pay\WechatPayException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BaseProvideV3
{
    public function __construct(
        protected Merchant $merchant,
        protected HttpClientInterface $client
    ) {
    }

    protected function getResult(ResponseInterface $response)
    {
        $code = $response->getStatusCode();
        if ($code !== 200) {
            $content = json_decode($response->getContent(false), true);
            if (JSON_ERROR_NONE === json_last_error()) {
                throw new WechatPayException($content['message'] ?? '', $code);
            }
        }
        return $response->toArray(true);
    }

    protected function getMerchantId(): string
    {
        return (string)$this->merchant->getMerchantId();
    }
}
