<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\QrCode;

use EasyWeChat\MiniProgram\QrCode\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/wxopen/qrcodejumpget')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list());
    }

    public function testGetVerifyFile()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/wxopen/qrcodejumpdownload')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getVerifyFile());
    }

    public function testSet()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'prefix' => 'https://www.qq.com/qr_code',
            'permit_sub_rule' => 1,
            'path' => 'pages/index/index',
            'open_version' => 1,
            'debug_url' => [
                'https://www.qq.com/qr_code?id=1'
            ],
            'is_edit' => 0,
        ];

        $client->expects()->httpPostJson('cgi-bin/wxopen/qrcodejumpadd', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->set($params));
    }

    public function testPublish()
    {
        $client = $this->mockApiClient(Client::class);

        $prefix = 'https://www.qq.com/qr_code';

        $client->expects()->httpPostJson('cgi-bin/wxopen/qrcodejumppublish', ['prefix' => $prefix])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->publish($prefix));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $prefix = 'https://www.qq.com/qr_code';

        $client->expects()->httpPostJson('cgi-bin/wxopen/qrcodejumpdelete', ['prefix' => $prefix])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete($prefix));
    }

    public function testShortUrl()
    {
        $client = $this->mockApiClient(Client::class);

        $long_url = 'https://www.qq.com/qr_code';

        $client->expects()->httpPostJson('cgi-bin/shorturl', ['long_url' => $long_url, 'action' => 'long2short'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->shortUrl($long_url));
    }
}
