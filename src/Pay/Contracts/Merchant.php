<?php

namespace EasyWeChat\Pay\Contracts;

interface Merchant
{
    public function getMerchantId(): int;
    public function getPrivateKey(): string;
    public function getCertificateSerialNumber(): string;
    public function getSecretKey(): string;
    public function getSubMerchant(): ?Merchant;
}
