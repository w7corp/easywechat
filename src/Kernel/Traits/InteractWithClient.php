<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;

trait InteractWithClient
{
    protected ?AccessTokenAwareClient $client = null;

    public function getClient(): AccessTokenAwareClient
    {
        if (! $this->client) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    public function setClient(AccessTokenAwareClient $client): static
    {
        $this->client = $client;

        return $this;
    }

    abstract public function createClient(): AccessTokenAwareClient;
}
