<?php

declare(strict_types=1);

namespace EasyWeChat\OpenPlatform;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket as VerifyTicketInterface;
use function is_string;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use function sprintf;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class VerifyTicket implements VerifyTicketInterface
{
    protected CacheInterface $cache;

    public function __construct(
        protected string $appId,
        protected ?string $key = null,
        ?CacheInterface $cache = null,
    ) {
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = sprintf('open_platform.verify_ticket.%s', $this->appId);
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setTicket(string $ticket): static
    {
        $this->cache->set($this->getKey(), $ticket, 6000);

        return $this;
    }

    /**
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function getTicket(): string
    {
        $ticket = $this->cache->get($this->getKey());

        if (! $ticket || ! is_string($ticket)) {
            throw new RuntimeException('No component_verify_ticket found.');
        }

        return $ticket;
    }
}
