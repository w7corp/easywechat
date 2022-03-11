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

        $strategy = new GenericRetryStrategy(
            $config['status_codes'],
            $config['delay'],
            $config['multiplier'],
            $config['max_delay'],
            $config['jitter'],
        );

        return $this->retryUsing($strategy, $config['max_retries']);
    }

    public function retryUsing(RetryStrategyInterface $strategy, int $maxRetries = 2, LoggerInterface $logger = null): static
    {
        $this->client = new RetryableHttpClient($this->client, $strategy, $maxRetries, $logger);

        return $this;
    }
}
