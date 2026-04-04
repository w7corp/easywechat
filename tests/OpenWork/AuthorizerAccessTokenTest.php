<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OpenWork;

use EasyWeChat\Kernel\Contracts\AccessToken as AccessTokenInterface;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\OpenWork\AuthorizerAccessToken;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthorizerAccessTokenTest extends TestCase
{
    public function test_get_corp_id_and_token_without_suite_access_token()
    {
        $token = new AuthorizerAccessToken('mock-corp-id', 'mock-access-token');

        $this->assertSame('mock-corp-id', $token->getCorpId());
        $this->assertSame('mock-access-token', $token->getToken());
        $this->assertSame('mock-access-token', $token->refresh());
        $this->assertSame('mock-access-token', (string) $token);
        $this->assertSame([
            'access_token' => 'mock-access-token',
        ], $token->toQuery());
    }

    public function test_get_token_from_http_request()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->shouldReceive('get')
            ->with('open_work.authorizer.access_token.mock-corp-id.mock-permanent-code')
            ->twice()
            ->andReturn(null, 'mock-access-token');
        $cache->expects()->set('open_work.authorizer.access_token.mock-corp-id.mock-permanent-code', 'mock-access-token', 7200)->andReturn(true);

        $suiteAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $suiteAccessToken->shouldReceive('getToken')->andReturn('mock-suite-access-token');

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'access_token' => 'mock-access-token',
            'expires_in' => 7200,
        ]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_corp_token', [
            'query' => [
                'suite_access_token' => 'mock-suite-access-token',
            ],
            'json' => [
                'auth_corpid' => 'mock-corp-id',
                'permanent_code' => 'mock-permanent-code',
            ],
        ])->andReturn($response);

        $token = new AuthorizerAccessToken(
            corpId: 'mock-corp-id',
            permanentCodeOrAccessToken: 'mock-permanent-code',
            suiteAccessToken: $suiteAccessToken,
            cache: $cache,
            httpClient: $httpClient,
        );

        $this->assertSame('mock-access-token', $token->getToken());
        $this->assertSame(['access_token' => 'mock-access-token'], $token->toQuery());
    }

    public function test_refresh_throws_exception_when_token_is_missing()
    {
        $suiteAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $suiteAccessToken->shouldReceive('getToken')->andReturn('mock-suite-access-token');

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn(['errcode' => 40013]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_corp_token', [
            'query' => [
                'suite_access_token' => 'mock-suite-access-token',
            ],
            'json' => [
                'auth_corpid' => 'mock-corp-id',
                'permanent_code' => 'mock-permanent-code',
            ],
        ])->andReturn($response);

        $token = new AuthorizerAccessToken(
            corpId: 'mock-corp-id',
            permanentCodeOrAccessToken: 'mock-permanent-code',
            suiteAccessToken: $suiteAccessToken,
            httpClient: $httpClient,
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Failed to get access_token: {"errcode":40013}');

        $token->refresh();
    }

    public function test_refresh_without_expires_in_does_not_trigger_warnings()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-cache-key')->andReturn(null);
        $cache->expects()->set('mock-cache-key', 'mock-access-token', 0)->andReturn(true);

        $suiteAccessToken = \Mockery::mock(AccessTokenInterface::class);
        $suiteAccessToken->shouldReceive('getToken')->andReturn('mock-suite-access-token');

        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'access_token' => 'mock-access-token',
        ]);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $httpClient->allows()->request('POST', 'cgi-bin/service/get_corp_token', [
            'query' => [
                'suite_access_token' => 'mock-suite-access-token',
            ],
            'json' => [
                'auth_corpid' => 'mock-corp-id',
                'permanent_code' => 'mock-permanent-code',
            ],
        ])->andReturn($response);

        $errors = [];
        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $token = new AuthorizerAccessToken(
                corpId: 'mock-corp-id',
                permanentCodeOrAccessToken: 'mock-permanent-code',
                suiteAccessToken: $suiteAccessToken,
                key: 'mock-cache-key',
                cache: $cache,
                httpClient: $httpClient,
            );

            $this->assertSame('mock-access-token', $token->getToken());
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
