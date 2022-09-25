<?php

declare(strict_types=1);

namespace EasyWeChat\OpenWork;

use EasyWeChat\Kernel\Exceptions\RuntimeException;
use EasyWeChat\OpenWork\Contracts\SuiteTicket as SuiteTicketInterface;
use function is_string;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use function sprintf;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class SuiteTicket implements SuiteTicketInterface
{
    protected CacheInterface $cache;

    public function __construct(
        protected string $suiteId,
        ?CacheInterface $cache = null,
        protected ?string $key = null,
    ) {
        $this->cache = $cache ?? new Psr16Cache(new FilesystemAdapter(namespace: 'easywechat', defaultLifetime: 1500));
    }

    public function getKey(): string
    {
        return $this->key ?? $this->key = sprintf('open_work.suite_ticket.%s', $this->suiteId);
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
            throw new RuntimeException('No suite_ticket found.');
        }

        return $ticket;
    }
}
