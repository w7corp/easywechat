<?php

namespace EasyWeChat\Tests\Kernel\HttpClient;

use EasyWeChat\Kernel\Contracts\AccessToken;
use EasyWeChat\Kernel\Contracts\RefreshableAccessToken;
use EasyWeChat\Kernel\HttpClient\AccessTokenExpiredRetryStrategy;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\AsyncContext;
use Symfony\Component\HttpClient\Response\MockResponse;

class AccessTokenExpiredRetryStrategyTest extends TestCase
{
    public function test_it_will_passthru_to_parent_retry_strategy()
    {
        $strategy = new AccessTokenExpiredRetryStrategy();

        // no decider: 200 should not be retried
        $context = $this->getContext(2, 'GET', 'http://easywechat.com', 200);
        $this->assertFalse($strategy->shouldRetry($context, 'mock-response', null));

        // no decider: 429 should be retried (default)
        $context = $this->getContext(2, 'GET', 'http://easywechat.com', 429);
        $this->assertTrue($strategy->shouldRetry($context, 'mock-response', null));
    }

    public function test_it_will_refresh_access_token_when_token_is_refreshable()
    {
        $strategy = new AccessTokenExpiredRetryStrategy();

        $notRefreshAbleAccessToken = \Mockery::mock(AccessToken::class, function ($mock) {
            $mock->shouldReceive('refresh')->never();
        });

        // no decider: 200 should not be retried
        $context = $this->getContext(2, 'GET', 'http://easywechat.com', 200);
        $this->assertFalse($strategy->shouldRetry($context, 'mock-response', null));

        // with not refreshable access token: 200 should not be retried
        $strategy->withAccessToken($notRefreshAbleAccessToken)
            ->decideUsing(function () {
                return true;
            });

        $context = $this->getContext(2, 'GET', 'http://easywechat.com', 200);
        $this->assertFalse($strategy->shouldRetry($context, 'mock-response', null));
        $this->assertFalse($strategy->shouldRetry($context, 'mock-response', null));

        // with refreshable access token and token expired: should be retried first time
        $refreshAbleAccessToken = \Mockery::mock(RefreshableAccessToken::class, function ($mock) {
            $mock->shouldReceive('refresh')->twice()->andReturns('mock-access-token', false);
        });

        $strategy->withAccessToken($refreshAbleAccessToken)
            ->decideUsing(function () {
                return true;
            });

        $context = $this->getContext(2, 'GET', 'http://easywechat.com', 200);

        // first time should be retried
        $this->assertTrue($strategy->shouldRetry($context, 'mock-response', null));

        // refresh failed(no refresh result string returned): should not be retried
        $this->assertFalse($strategy->shouldRetry($context, 'mock-response', null));
    }

    private function getContext($retryCount, $method, $url, $statusCode): AsyncContext
    {
        $passthru = null;
        $info = [
            'retry_count' => $retryCount,
            'http_method' => $method,
            'url' => $url,
            'http_code' => $statusCode,
        ];
        $response = new MockResponse('', $info);

        return new AsyncContext($passthru, new MockHttpClient(), $response, $info, null, 0);
    }
}
