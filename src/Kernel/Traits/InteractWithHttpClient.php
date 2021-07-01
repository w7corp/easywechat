<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Support\UserAgent;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

    protected function createHttpClient(): HttpClientInterface
    {
        $scopedHttpClientClass = \sprintf('%s\HttpClient', (new \ReflectionClass($this))->getNamespaceName());

        if (\class_exists($scopedHttpClientClass)) {
            $client = new $scopedHttpClientClass();
        } else {
            $client = HttpClient::create();
        }

        $defaultOptions = $this->getHttpClientDefaultOptions();

        if (empty($defaultOptions['headers']['User-Agent'])) {
            $defaultOptions['headers']['User-Agent'] = UserAgent::create([$defaultOptions['headers']['User-Agent'] ?? '']);
        }

        return $client->withOptions($defaultOptions);
    }

    protected function getHttpClientDefaultOptions(): array
    {
        return [];
    }
}
