<?php

namespace EasyWeChat\Kernel\HttpClient;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\Retry\RetryStrategyInterface;
use Symfony\Component\HttpClient\RetryableHttpClient;

trait RetryableClient
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function retry(array $config = []): static
    {
        $config = RequestUtil::mergeDefaultRetryOptions($config);

        /** @phpstan-ignore-next-line */
        $strategy = new GenericRetryStrategy($config['status_codes'], $config['delay'], $config['multiplier'], $config['max_delay'], $config['jitter']);

        /** @phpstan-ignore-next-line */
        return $this->retryUsing($strategy, (int) $config['max_retries']);
    }

    public function retryUsing(RetryStrategyInterface $strategy, int $maxRetries = 3, LoggerInterface $logger = null): static
    {
        $this->client = new RetryableHttpClient($this->client, $strategy, $maxRetries, $logger);

        return $this;
    }
}
