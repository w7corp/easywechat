<?php

namespace EasyWeChat\Pay;

use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;

class Merchant implements MerchantInterface
{
    public function __construct(
        protected int $appId,
        protected string $privateKey,
        protected string $secretKey,
        protected string $certificate,
        protected string $certificateSerialNo,
    ) {
    }

    public function getMerchantId(): int
    {
        return $this->appId;
    }

    public function getPrivateKey(): string
    {
        return file_exists($this->privateKey) ? \file_get_contents($this->privateKey) : $this->privateKey;
    }

    public function getCertificate(): string
    {
        return file_exists($this->certificate) ? \file_get_contents($this->certificate) : $this->certificate;
    }

    public function getCertificateSerialNumber(): string
    {
        return $this->certificateSerialNo;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
}
