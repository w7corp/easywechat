<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\UriBuilder;

trait InteractWithAccessTokenClient
{
    protected ?UriBuilder $client = null;

    public function getClient(): UriBuilder
    {
        if (!$this->client) {
            $this->client = new UriBuilder(client: $this->getHttpClient()->withAccessToken($this->getAccessToken()));
        }

        return $this->client;
    }

    public function setClient(UriBuilder $client): static
    {
        $this->client = $client;

        return $this;
    }
}
