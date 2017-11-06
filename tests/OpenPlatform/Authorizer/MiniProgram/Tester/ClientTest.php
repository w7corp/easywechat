<?php
/**
 * Created by PhpStorm.
 * User: keal
 * Date: 2017/11/6
 * Time: 下午2:39
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\MiniProgram\Tester;


use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Tester\Client;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testBind()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/bind_tester', ['wechatid' => 'bar'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->bind('bar'));
    }

    public function testUnbind()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/unbind_tester', ['wechatid' => 'bar'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->unbind('bar'));
    }
}