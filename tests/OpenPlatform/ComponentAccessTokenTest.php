<?php

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\ComponentAccessToken;
use EasyWeChat\OpenPlatform\Contracts\VerifyTicket;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ComponentAccessTokenTest extends TestCase
{
    public function test_set_and_get_cache_key()
    {
        $token = new ComponentAccessToken('mock-app-id', 'mock-secret', \Mockery::mock(VerifyTicket::class));

        $this->assertSame('open_platform.component_access_token.mock-app-id', $token->getKey());

        $token->setKey('custom-cache-key-for-app-id');
        $this->assertSame('custom-cache-key-for-app-id', $token->getKey());
    }

    public function test_get_token_from_cache()
    {
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('open_platform.component_access_token.mock-app-id')->andReturns('mock-cached-access-token')->twice();
        $token = new ComponentAccessToken('mock-app-id', 'mock-secret', \Mockery::mock(VerifyTicket::class), cache: $cache);

        $this->assertSame('mock-cached-access-token', $token->getToken());

        // to query
        $this->assertSame([
            'component_access_token' => 'mock-cached-access-token',
        ], $token->toQuery());
    }

    public function test_get_token_from_server()
    {
        $verifyTicket = \Mockery::mock(VerifyTicket::class);
        $verifyTicket->expects()->getTicket()->andReturns('mock-verify-ticket');
        $response = [
            'component_access_token' => 'mock-access-token',
            'expires_in' => 2700,
        ];

        // cache client
        $cache = \Mockery::mock(CacheInterface::class);
        $cache->expects()->get('open_platform.component_access_token.mock-app-id')->andReturns(null)->once();
        $cache->expects()->set('open_platform.component_access_token.mock-app-id', 'mock-access-token', 2700 - 100)->once();

        // http client
        $mockResponse = new MockResponse(\json_encode($response));
        $httpClient = new MockHttpClient($mockResponse, 'https://api.weixin.qq.com/');

        $token = new ComponentAccessToken('mock-app-id', 'mock-secret', $verifyTicket, httpClient: $httpClient, cache: $cache);

        $this->assertSame('mock-access-token', $token->getToken());

        $this->assertSame('POST', $mockResponse->getRequestMethod());
        $this->assertSame('https://api.weixin.qq.com/cgi-bin/component/api_component_token', $mockResponse->getRequestUrl());
        $this->assertSame(\json_encode([
            'component_appid' => 'mock-app-id',
            'component_appsecret' => 'mock-secret',
            'component_verify_ticket' => 'mock-verify-ticket',
        ]), $mockResponse->getRequestOptions()['body']);
    }
}
