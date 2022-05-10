<?php

declare(strict_types=1);

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\OfficialAccount\Contracts\Account as AccountInterface;
use RuntimeException;

class Account implements AccountInterface
{
    public function __construct(
        protected string $appId,
        protected ?string $secret,
        protected ?string $token = null,
        protected ?string $aesKey = null
    ) {
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getSecret(): string
    {
        if (null === $this->secret) {
            throw new RuntimeException('No secret configured.');
        }

        return $this->secret;
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
