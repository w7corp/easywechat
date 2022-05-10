<?php

declare(strict_types=1);

namespace EasyWeChat\Pay\Contracts;

use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;

interface Merchant
{
    public function getMerchantId(): int;

    public function getPrivateKey(): PrivateKey;

    public function getSecretKey(): string;

    public function getV2SecretKey(): ?string;

    public function getCertificate(): PublicKey;

    public function getPlatformCert(string $serial): ?PublicKey;
}
