<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Work;

use EasyWeChat\Work\AccessToken;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

        $response->allows()->toArray()->andReturn($result);

        $config = [
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
        ];

        $options = [
            'query' => [
                'corpid' => $config['corp_id'],
                'corpsecret' => $config['secret'],
            ],
        ];

        $httpClient->allows()->request('GET', '/cgi-bin/gettoken', $options)->andReturn($response);

        $accessToken = new AccessToken($config['corp_id'], $config['secret'], null, null, $httpClient);

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
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
        ];

        $accessToken = new AccessToken($config['corp_id'], $config['secret'], $key, $cache);

        $this->assertSame($result['access_token'], $accessToken->getToken());
    }

    public function test_set_key()
    {
        $config = [
            'corp_id' => 'wx3cf0f39249000060',
            'secret' => 'mock-secret',
        ];

        $key = 'mock-cache-key';

        $accessToken = new AccessToken($config['corp_id'], $config['secret'], $key);

        $this->assertSame($key, $accessToken->getKey());
    }
}
