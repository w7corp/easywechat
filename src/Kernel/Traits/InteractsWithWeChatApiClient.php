<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\AccessTokenExpiredRetryStrategy;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\HttpClient\Response;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\RetryableHttpClient;

use function str_contains;

trait InteractsWithWeChatApiClient
{
    protected function createWeChatApiClient(callable $failureJudge): AccessTokenAwareClient
    {
        $httpClient = $this->getHttpClient();

        if ((bool) $this->config->get('http.retry', false)) {
            $httpClient = new RetryableHttpClient(
                $httpClient,
                $this->getRetryStrategy(),
                (int) $this->config->get('http.max_retries', 2) // @phpstan-ignore-line
            );
        }

        return (new AccessTokenAwareClient(
            client: $httpClient,
            accessToken: $this->getAccessToken(),
            failureJudge: fn (Response $response): bool => (bool) $failureJudge($response),
            throw: (bool) $this->config->get('http.throw', true),
        ))->setPresets($this->config->all());
    }

    public function getRetryStrategy(): AccessTokenExpiredRetryStrategy
    {
        $retryConfig = RequestUtil::mergeDefaultRetryOptions((array) $this->config->get('http.retry', []));

        return (new AccessTokenExpiredRetryStrategy($retryConfig))
            ->decideUsing(function (AsyncContext $context, ?string $responseContent): bool {
                return ! empty($responseContent)
                    && str_contains($responseContent, '42001')
                    && str_contains($responseContent, 'access_token expired');
            });
    }
}
