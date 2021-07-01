<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Contracts\AccessTokenAwareHttpClient as HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;

trait InteractWithHttpClient
{
    protected ?HttpClientInterface $httpClient = null;

    public function getHttpClient(): HttpClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = $this->createHttpClient();
        }

        return $this->httpClient;
    }

    public function setHttpClient(HttpClientInterface $httpClient): static
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    protected function createHttpClient()
    {
        $scopedHttpClientClass = \sprintf('%s\HttpClient', (new \ReflectionClass($this))->getNamespaceName());

        if (\class_exists($scopedHttpClientClass)) {
            $client = new $scopedHttpClientClass();
        } else {
            $client = HttpClient::create();
        }

        $client->withOptions($this->getHttpClientDefaultOptions());

        return $client;
    }

    protected function getHttpClientDefaultOptions()
    {
        return $this->config->get('http', []);
    }
}
