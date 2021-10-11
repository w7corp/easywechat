<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\Kernel\Traits\InteractWithCache;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;

class VerifyTicket implements VerifyTicketInterface
{
    use InteractWithCache;

    public function __construct(
        protected string $appId,
        protected ?string $key = null,
    ) {
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = \sprintf('open_platform.verify_ticket.%s', $this->appId);
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setTicket(string $ticket): static
    {
        $this->getCache()->set($this->getKey(), $ticket, 6000);

        return $this;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getTicket(): string
    {
        $ticket = $this->getCache()->get($this->getKey());

        if (!$ticket) {
            throw new RuntimeException('No component_verify_ticket found.');
        }

        return $ticket;
    }
}
