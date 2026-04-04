<?php

declare(strict_types=1);

namespace EasyWeChat\Kernel\Traits;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Kernel\HttpClient\AccessTokenExpiredRetryStrategy;
use EasyWeChat\Kernel\HttpClient\RequestUtil;
use EasyWeChat\Kernel\HttpClient\Response;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\RetryableHttpClient;

use function str_contains;

trait InteractsWithWeChatApiClient
{
    protected function createWeChatApiClient(
        AccessTokenInterface $accessToken,
        callable $failureJudge,
    ): AccessTokenAwareClient {
        return $this->createAccessTokenAwareClient(
            accessToken: $accessToken,
            failureJudge: $failureJudge,
            retryWhenConfigured: true,
        );
    }

    protected function createErrcodeAwareClient(AccessTokenInterface $accessToken): AccessTokenAwareClient
    {
        return $this->createAccessTokenAwareClient(
            accessToken: $accessToken,
            failureJudge: fn (Response $response): bool => (bool) ($response->toArray()['errcode'] ?? 0),
        );
    }

    protected function createAccessTokenAwareClient(
        AccessTokenInterface $accessToken,
        callable $failureJudge,
        bool $retryWhenConfigured = false,
    ): AccessTokenAwareClient {
        $httpClient = $this->getHttpClient();

        if ($retryWhenConfigured && (bool) $this->config->get('http.retry', false)) {
            $httpClient = new RetryableHttpClient(
                $httpClient,
                $this->getRetryStrategy(),
                $this->getIntConfig('http.max_retries', 2)
            );
        }

        return (new AccessTokenAwareClient(
            client: $httpClient,
            accessToken: $accessToken,
            failureJudge: fn (Response $response): bool => (bool) $failureJudge($response),
            throw: $this->getBoolConfig('http.throw', true),
        ))->setPresets($this->config->all());
    }

    public function getRetryStrategy(): AccessTokenExpiredRetryStrategy
    {
        /** @var array<string, mixed> $retryOptions */
        $retryOptions = (array) $this->config->get('http.retry', []);
        $retryConfig = RequestUtil::mergeDefaultRetryOptions($retryOptions);

        return (new AccessTokenExpiredRetryStrategy($retryConfig))
            ->decideUsing(function (AsyncContext $context, ?string $responseContent): bool {
                return ! empty($responseContent)
                    && str_contains($responseContent, '42001')
                    && str_contains($responseContent, 'access_token expired');
            });
    }
}
