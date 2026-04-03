<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;

trait InteractWithClient
{
    protected ?AccessTokenAwareClient $client = null;

    protected bool $usesCustomClient = false;

    public function getClient(): AccessTokenAwareClient
    {
        if (! $this->client) {
            $this->client = $this->createClient();
            $this->usesCustomClient = false;
        }

        return $this->client;
    }

    public function setClient(AccessTokenAwareClient $client): static
    {
        $this->client = $client;
        $this->usesCustomClient = true;

        return $this;
    }

    protected function resetClient(): void
    {
        if ($this->usesCustomClient) {
            return;
        }

        $this->client = null;
    }

    abstract public function createClient(): AccessTokenAwareClient;
}
