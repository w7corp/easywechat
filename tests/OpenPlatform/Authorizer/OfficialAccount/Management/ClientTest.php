<?php
/**
 * Created by PhpStorm.
 * User: keal
 * Date: 2018/4/3
 * Time: 下午2:33
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\OfficialAccount\Management;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Management\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testList()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('cgi-bin/wxopen/wxamplinkget')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list());
    }

    public function testLink()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('cgi-bin/wxopen/wxamplink', [
            'appid' => 'wxa',
            'notify_users' => false,
            'show_profile' => true
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->link('wxa', false, true));
    }

    public function testUnlink()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('cgi-bin/wxopen/wxampunlink', [
            'appid' => 'wxa'
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->unlink('wxa'));
    }

    public function testRegister()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('cgi-bin/account/fastregister', [
            'ticket' => 'ticket'
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->register('ticket'));
    }
}