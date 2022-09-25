<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Kernel\HttpClient;

use EasyWeChat\Kernel\Contracts\AccessToken;
use EasyWeChat\Kernel\HttpClient\AccessTokenAwareClient;
use EasyWeChat\Tests\TestCase;

class AccessTokenAwareClientTest extends TestCase
{
    public function test_full_uri_call()
    {
        $client = AccessTokenAwareClient::mock();

        $options = [
            'headers' => [
                'accept' => 'application/json',
            ],
        ];

        $client->request('GET', 'https://api2.mch.weixin.qq.com/v3/certificates', $options);

        $this->assertSame('GET', $client->getRequestMethod());
        $this->assertSame('https://api2.mch.weixin.qq.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame(['accept: application/json'], $client->getRequestOptions()['headers']);
    }

    public function test_shortcuts_call()
    {
        $client = AccessTokenAwareClient::mock();

        $client->get('v3/certificates', [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        $this->assertSame('GET', $client->getRequestMethod());
        $this->assertSame('https://example.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame(['accept: application/json'], $client->getRequestOptions()['headers']);
    }

    public function test_it_will_auto_wrap_body()
    {
        $client = AccessTokenAwareClient::mock();

        $client->post('v3/certificates', [
            'body' => [
                'foo' => 'bar',
            ],
        ]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://example.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame('foo=bar', $client->getRequestOptions()['body']);

        // post without body key
        $client = AccessTokenAwareClient::mock();
        $client->post('v3/certificates', [
            'foo' => 'bar',
        ]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://example.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame('foo=bar', $client->getRequestOptions()['body']);

        // patch without body key
        $client = AccessTokenAwareClient::mock();
        $client->patch('v3/certificates', [
            'foo' => 'bar',
        ]);

        $this->assertSame('PATCH', $client->getRequestMethod());
        $this->assertSame('https://example.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame('foo=bar', $client->getRequestOptions()['body']);

        // put without body key
        $client = AccessTokenAwareClient::mock();
        $client->put('v3/certificates', [
            'foo' => 'bar',
        ]);

        $this->assertSame('PUT', $client->getRequestMethod());
        $this->assertSame('https://example.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame('foo=bar', $client->getRequestOptions()['body']);
    }

    public function test_it_will_apply_access_token_to_query()
    {
        $client = AccessTokenAwareClient::mock();

        $client->withAccessToken(new class() implements AccessToken
        {
            public function getToken(): string
            {
                return 'mock-access-token';
            }

            public function toQuery(): array
            {
                return ['access_token' => 'mock-access-token'];
            }
        });

        $client->get('v3/certificates', ['foo' => 'bar']);

        $this->assertSame('https://example.com/v3/certificates?foo=bar&access_token=mock-access-token', $client->getRequestUrl());
    }
}
