<?php

declare(strict_types=1);

namespace EasyWeChat\Work;

use EasyWeChat\Work\Contracts\Account as AccountInterface;

class Account implements AccountInterface
{
    public function __construct(
        protected string $corpId,
        protected string $secret,
        protected ?string $token = null,
        protected ?string $aesKey = null,
        protected ?int $agentId = null
    ) {
    }

    public function getCorpId(): string
    {
        return $this->corpId;
    }

    public function getSecret(): string
    {
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

    public function getAgentId(): int
    {
        return $this->agentId;
    }
}
