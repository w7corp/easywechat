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
            // @phpstan-ignore-next-line
            (array) $config['status_codes'],
            // @phpstan-ignore-next-line
            (int) $config['delay'],
            // @phpstan-ignore-next-line
            (float) $config['multiplier'],
            // @phpstan-ignore-next-line
            (int) $config['max_delay'],
            // @phpstan-ignore-next-line
            (float) $config['jitter']
        );

        /** @phpstan-ignore-next-line */
        return $this->retryUsing($strategy, (int) $config['max_retries']);
    }

    public function retryUsing(
        RetryStrategyInterface $strategy,
        int $maxRetries = 3,
        LoggerInterface $logger = null
    ): static {
        $this->client = new RetryableHttpClient($this->client, $strategy, $maxRetries, $logger);

        return $this;
    }
}
