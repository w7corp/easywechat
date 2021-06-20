<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;

class Account implements AccountInterface
{
    public function __construct(
        protected string $corpId,
        protected string $providerSecret,
        protected ?string $token = null,
        protected ?string $aesKey = null
    ) {
    }

    public function getCorpId(): string
    {
        return $this->corpId;
    }

    public function getProviderSecret(): string
    {
        return $this->providerSecret;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function getAesKey(): ?string
    {
        return $this->aesKey;
    }
}
