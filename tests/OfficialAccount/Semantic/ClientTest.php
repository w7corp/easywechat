<?php


namespace EasyWeChat\Tests\OfficialAccount\Semantic;


use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OfficialAccount\Semantic\Client;
use EasyWeChat\Tests\TestCase;


class ClientTest extends TestCase
{
    public function testQuery()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('semantic/semproxy/search', [
            'query' => 'keywords',
            'category' => ['foo', 'bar'],
            'appid' => '123456',
            'name' => 'easywechat',
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->query('keywords', ['foo', 'bar'], ['name' => 'easywechat']));
    }
}
