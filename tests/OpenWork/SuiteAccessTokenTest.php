<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OpenWork\Contracts\SuiteTicket as SuiteTicketInterface;
use EasyWeChat\OpenWork\SuiteAccessToken;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SuiteAccessTokenTest extends TestCase
{
    public function test_get_token_from_http_request()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->shouldReceive('get')
            ->with('open_work.suite_access_token.mock-suite-id.mock-suite-secret')
            ->twice()
            ->andReturn(null, 'mock-suite-access-token');
        $cache->expects()->set('open_work.suite_access_token.mock-suite-id.mock-suite-secret', 'mock-suite-access-token', 7100)->andReturn(true);

        $suiteTicket = \Mockery::mock(SuiteTicketInterface::class);
        $suiteTicket->shouldReceive('getTicket')->andReturn('mock-suite-ticket');

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'suite_access_token' => 'mock-suite-access-token',
            'expires_in' => 7200,
        ]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_suite_token', [
            'json' => [
                'suite_id' => 'mock-suite-id',
                'suite_secret' => 'mock-suite-secret',
                'suite_ticket' => 'mock-suite-ticket',
            ],
        ])->andReturn($response);

        $accessToken = new SuiteAccessToken(
            suiteId: 'mock-suite-id',
            suiteSecret: 'mock-suite-secret',
            suiteTicket: $suiteTicket,
            cache: $cache,
            httpClient: $httpClient,
        );

        $this->assertSame('mock-suite-access-token', $accessToken->getToken());
        $this->assertSame(['suite_access_token' => 'mock-suite-access-token'], $accessToken->toQuery());
    }

    public function test_get_token_from_cache()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-cache-key')->andReturn('mock-cached-access-token');

        $accessToken = new SuiteAccessToken(
            suiteId: 'mock-suite-id',
            suiteSecret: 'mock-suite-secret',
            key: 'mock-cache-key',
            cache: $cache,
        );

        $this->assertSame('mock-cached-access-token', $accessToken->getToken());
        $this->assertSame('mock-cache-key', $accessToken->getKey());
    }

    public function test_refresh_throws_exception_when_token_is_missing()
    {
        $suiteTicket = \Mockery::mock(SuiteTicketInterface::class);
        $suiteTicket->shouldReceive('getTicket')->andReturn('mock-suite-ticket');

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn(['errcode' => 40013]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_suite_token', [
            'json' => [
                'suite_id' => 'mock-suite-id',
                'suite_secret' => 'mock-suite-secret',
                'suite_ticket' => 'mock-suite-ticket',
            ],
        ])->andReturn($response);

        $accessToken = new SuiteAccessToken(
            suiteId: 'mock-suite-id',
            suiteSecret: 'mock-suite-secret',
            suiteTicket: $suiteTicket,
            httpClient: $httpClient,
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get suite_access_token: {"errcode":40013}');

        $accessToken->refresh();
    }

    public function test_refresh_without_expires_in_does_not_trigger_warnings()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-cache-key')->andReturn(null);
        $cache->expects()->set('mock-cache-key', 'mock-suite-access-token', 100)->andReturn(true);

        $suiteTicket = \Mockery::mock(SuiteTicketInterface::class);
        $suiteTicket->shouldReceive('getTicket')->andReturn('mock-suite-ticket');

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'suite_access_token' => 'mock-suite-access-token',
        ]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_suite_token', [
            'json' => [
                'suite_id' => 'mock-suite-id',
                'suite_secret' => 'mock-suite-secret',
                'suite_ticket' => 'mock-suite-ticket',
            ],
        ])->andReturn($response);

        $errors = [];
        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $accessToken = new SuiteAccessToken(
                suiteId: 'mock-suite-id',
                suiteSecret: 'mock-suite-secret',
                suiteTicket: $suiteTicket,
                key: 'mock-cache-key',
                cache: $cache,
                httpClient: $httpClient,
            );

            $this->assertSame('mock-suite-access-token', $accessToken->getToken());
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
