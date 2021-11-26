<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\OpenWork\Contracts\Account as AccountInterface;

class Account implements AccountInterface
{
    public function __construct(
        protected string $corpId,
        protected string $providerSecret,
        protected string $suiteId,
        protected string $suiteSecret,
        protected string $token,
        protected string $aesKey
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

    public function getSuiteId(): string
    {
        return $this->suiteId;
    }

    public function getSuiteSecret(): string
    {
        return $this->suiteSecret;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getAesKey(): string
    {
        return $this->aesKey;
    }
}
