<?php


namespace EasyWeChat\Tests\OfficialAccount\Fundamental;


use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OfficialAccount\Fundamental\Client;
use EasyWeChat\Tests\TestCase;


class ClientTest extends TestCase
{
    public function testClearQuota()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('cgi-bin/clear_quota', [
            'appid' => '123456',
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->clearQuota());
    }

    public function testGetCallbackIp()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/getcallbackip')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->getCallbackIp());
    }
}
