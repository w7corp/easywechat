<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\OfficialAccount;

use EasyWeChat\OfficialAccount\AccessToken;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AccessTokenTest extends TestCase
{
    public function test_get_token_from_http_request()
    {
        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);

        $result = [
            'access_token' => 'mock_access_token',
            'expires_in' => '1500',
        ];

        $response->allows()->toArray(false)->andReturn($result);

        $config = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
        ];

        $options = [
            'query' => [
                'grant_type' => 'client_credential',
                'appid' => $config['app_id'],
                'secret' => $config['secret'],
            ],
        ];

        $httpClient->allows()->request('GET', 'cgi-bin/token', $options)->andReturn($response);

        $accessToken = new AccessToken($config['app_id'], $config['secret'], null, null, $httpClient);

        $this->assertSame($result['access_token'], $accessToken->getToken());
    }

    public function test_get_token_from_cache()
    {
        $cache = \Mockery::mock(CacheInterface::class);

        $key = 'mock-cache-key';

        $result = [
            'access_token' => 'mock_access_token',
            'expires_in' => '1500',
        ];

        $cache->expects()->get($key)->andReturn($result['access_token']);

        $config = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
        ];

        $accessToken = new AccessToken($config['app_id'], $config['secret'], $key, $cache);

        $this->assertSame($result['access_token'], $accessToken->getToken());
    }

    public function test_set_key()
    {
        $config = [
            'app_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
        ];

        $key = 'mock-cache-key';

        $accessToken = new AccessToken($config['app_id'], $config['secret'], $key);

        $this->assertSame($key, $accessToken->getKey());
    }

    public function test_get_token_without_expires_in_does_not_trigger_warnings()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('mock-cache-key')->andReturn(null);
        $cache->expects()->set('mock-cache-key', 'mock_access_token', 0)->andReturn(true);

        $httpClient = \Mockery::mock(HttpClientInterface::class);
        $response = \Mockery::mock(ResponseInterface::class);
        $response->allows()->toArray(false)->andReturn([
            'access_token' => 'mock_access_token',
        ]);

        $httpClient->allows()->request('GET', 'cgi-bin/token', [
            'query' => [
                'grant_type' => 'client_credential',
                'appid' => 'wx3cf0f39249000060',
                'secret' => 'mock-secret',
            ],
        ])->andReturn($response);

        $errors = [];
        set_error_handler(function (int $severity, string $message) use (&$errors): bool {
            $errors[] = [$severity, $message];

            return true;
        });

        try {
            $accessToken = new AccessToken('wx3cf0f39249000060', 'mock-secret', 'mock-cache-key', $cache, $httpClient);

            $this->assertSame('mock_access_token', $accessToken->getToken());
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $errors);
    }
}
