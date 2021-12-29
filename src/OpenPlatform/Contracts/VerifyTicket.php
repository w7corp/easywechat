<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform\Contracts;

interface VerifyTicket
{
    public function getKey(): string;
    public function setKey(string $key): static;
    public function getTicket(): string;
    public function setTicket(string $ticket): static;
}
