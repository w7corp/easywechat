<?php

namespace EasyWeChat\OfficialAccount;

use EasyWeChat\OfficialAccount\Contracts\Account as AccountContract;

class Account implements AccountContract
{
    public function __construct(
        protected string $appId,
        protected string $secret,
        protected string $aesKey,
        protected string $token
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
