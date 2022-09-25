<?php

declare(strict_types=1);

namespace EasyWeChat\Pay;

use function array_is_list;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Support\PrivateKey;
use EasyWeChat\Kernel\Support\PublicKey;
use EasyWeChat\Pay\Contracts\Merchant as MerchantInterface;
use function intval;
use function is_string;

class Merchant implements MerchantInterface
{
    /**
     * @var array<string, PublicKey>
     */
    protected array $platformCerts = [];

    /**
     * @param  array<int|string, string|PublicKey>  $platformCerts
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public function __construct(
        protected int|string $mchId,
        protected PrivateKey $privateKey,
        protected PublicKey $certificate,
        protected string $secretKey,
        protected ?string $v2SecretKey = null,
        array $platformCerts = [],
    ) {
        $this->platformCerts = $this->normalizePlatformCerts($platformCerts);
    }

    public function getMerchantId(): int
    {
        return intval($this->mchId);
    }

    public function getPrivateKey(): PrivateKey
    {
        return $this->privateKey;
    }

    public function getCertificate(): PublicKey
    {
        return $this->certificate;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getV2SecretKey(): ?string
    {
        return $this->v2SecretKey;
    }

    public function getPlatformCert(string $serial): ?PublicKey
    {
        return $this->platformCerts[$serial] ?? null;
    }

    /**
     * @param  array<array-key, string|PublicKey>  $platformCerts
     * @return array<string, PublicKey>
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    protected function normalizePlatformCerts(array $platformCerts): array
    {
        $certs = [];
        $isList = array_is_list($platformCerts);
        foreach ($platformCerts as $index => $publicKey) {
            if (is_string($publicKey)) {
                $publicKey = new PublicKey($publicKey);
            }

            if (! $publicKey instanceof PublicKey) {
                throw new InvalidArgumentException('Invalid platform certficate.');
            }

            $certs[$isList ? $publicKey->getSerialNo() : $index] = $publicKey;
        }

        return $certs;
    }
}
