<?php


namespace EasyWeChat\Tests\BaseService\Url;

use EasyWeChat\BaseService\Url\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testShorten()
    {
        $client = $this->mockApiClient(Client::class);
        $url = 'http://easywechat.com';
        $client->expects()->httpPostJson('cgi-bin/shorturl', [
            'action' => 'long2short',
            'long_url' => $url,
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->shorten($url));
    }
}
