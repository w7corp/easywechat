<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OpenWork\ProviderAccessToken;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ProviderAccessTokenTest extends TestCase
{
    public function test_get_token_from_http_request()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->shouldReceive('get')
            ->with('open_work.access_token.mock-corp-id.mock-provider-secret')
            ->twice()
            ->andReturn(null, 'mock-access-token');
        $cache->expects()->set('open_work.access_token.mock-corp-id.mock-provider-secret', 'mock-access-token', 7200)->andReturn(true);

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'provider_access_token' => 'mock-access-token',
            'expires_in' => 7200,
        ]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_provider_token', [
            'json' => [
                'corpid' => 'mock-corp-id',
                'provider_secret' => 'mock-provider-secret',
            ],
        ])->andReturn($response);

        $accessToken = new ProviderAccessToken(
            corpId: 'mock-corp-id',
            providerSecret: 'mock-provider-secret',
            cache: $cache,
            httpClient: $httpClient,
        );

        $this->assertSame('mock-access-token', $accessToken->getToken());
        $this->assertSame(['provider_access_token' => 'mock-access-token'], $accessToken->toQuery());
    }

    public function test_get_token_from_cache()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-cache-key')->andReturn('mock-cached-access-token');

        $accessToken = new ProviderAccessToken(
            corpId: 'mock-corp-id',
            providerSecret: 'mock-provider-secret',
            key: 'mock-cache-key',
            cache: $cache,
        );

        $this->assertSame('mock-cached-access-token', $accessToken->getToken());
        $this->assertSame('mock-cache-key', $accessToken->getKey());
    }

    public function test_refresh_throws_exception_when_token_is_missing()
    {
        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn(['errcode' => 40013]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_provider_token', [
            'json' => [
                'corpid' => 'mock-corp-id',
                'provider_secret' => 'mock-provider-secret',
            ],
        ])->andReturn($response);

        $accessToken = new ProviderAccessToken(
            corpId: 'mock-corp-id',
            providerSecret: 'mock-provider-secret',
            httpClient: $httpClient,
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get provider_access_token: {"errcode":40013}');

        $accessToken->refresh();
    }

    public function test_refresh_without_expires_in_does_not_trigger_warnings()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-cache-key')->andReturn(null);
        $cache->expects()->set('mock-cache-key', 'mock-access-token', 0)->andReturn(true);

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'provider_access_token' => 'mock-access-token',
        ]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_provider_token', [
            'json' => [
                'corpid' => 'mock-corp-id',
                'provider_secret' => 'mock-provider-secret',
            ],
        ])->andReturn($response);

        $errors = [];
        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $accessToken = new ProviderAccessToken(
                corpId: 'mock-corp-id',
                providerSecret: 'mock-provider-secret',
                key: 'mock-cache-key',
                cache: $cache,
                httpClient: $httpClient,
            );

            $this->assertSame('mock-access-token', $accessToken->getToken());
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
