<?php

declare(strict_types=1);

namespace EasyWeChat\Tests\Pay;

use EasyWeChat\Pay\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function test_v3_request()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->andReturn('mock-signature');

        $options = [
            'headers' => [
                'accept' => 'application/json',
            ],
        ];

        $client->request('GET', 'https://api2.mch.weixin.qq.com/v3/certificates', $options);

        $this->assertSame('GET', $client->getRequestMethod());
        $this->assertSame('https://api2.mch.weixin.qq.com/v3/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: application/json', $client->getRequestOptions()['headers'][2]);
        $this->assertSame('accept: application/json', $client->getRequestOptions()['headers'][0]);
    }

    public function test_v2_request()
    {
        $client = Client::mock();
        $client->shouldReceive('createSignature')->never();
        $client->shouldReceive('attachLegacySignature')->with([
            'foo' => 'bar',
        ])->andReturn(['foo' => 'bar', 'sign' => 'mock-signature']);

        $client->post('certificates', [
            'body' => \json_encode([
                'foo' => 'bar',
            ]),
        ]);

        $this->assertSame('POST', $client->getRequestMethod());
        $this->assertSame('https://api.mch.weixin.qq.com/certificates', $client->getRequestUrl());
        $this->assertSame('Content-Type: application/json', $client->getRequestOptions()['headers'][1]);
    }
}
