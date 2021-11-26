<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\OpenPlatform\Contracts\Account as AccountInterface;

class Account implements AccountInterface
{
    public function __construct(
        protected string $appId,
        protected string $secret,
        protected string $token,
        protected string $aesKey
    ) {
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getSecret(): string
    {
        return $this->secret;
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
