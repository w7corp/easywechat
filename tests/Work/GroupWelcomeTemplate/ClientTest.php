<?php

namespace EasyWeChat\Tests\Work\GroupWelcomeTemplate;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\GroupWelcomeTemplate\Client;

/**
 * 入群欢迎语素材管理
 *
 * @package EasyWeChat\Tests\Work\GroupWelcomeTemplate
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class ClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/externalcontact/group_welcome_template/add', [
            'text' => [
                'content' => '亲爱的%NICKNAME%用户，你好'
            ],
            'video' => [
                'media_id' => '3slzN7WlXH1Ngy3Z4hq0AQZKqKd3l9F23Tjac1i4qqWidD1QCaJTW7sEBhQQVNLe5'
            ]
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add([
            'text' => [
                'content' => '亲爱的%NICKNAME%用户，你好'
            ],
            'video' => [
                'media_id' => '3slzN7WlXH1Ngy3Z4hq0AQZKqKd3l9F23Tjac1i4qqWidD1QCaJTW7sEBhQQVNLe5'
            ]
        ]));
    }

    public function testEdit()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/externalcontact/group_welcome_template/edit', [
            'template_id' => 'msg2MgBEgAATurBYDPgS32DfSt5vdzaHA',
            'text' => [
                'content' => '亲爱的%NICKNAME%用户，你好呀'
            ],
            'video' => [
                'media_id' => '3slzN7WlXH1Ngy3Z4hq0AQZKqKd3l9F23Tjac1i4qqWidD1QCaJTW7sEBhQQVNLe5'
            ]
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->edit('msg2MgBEgAATurBYDPgS32DfSt5vdzaHA', [
            'text' => [
                'content' => '亲爱的%NICKNAME%用户，你好呀'
            ],
            'video' => [
                'media_id' => '3slzN7WlXH1Ngy3Z4hq0AQZKqKd3l9F23Tjac1i4qqWidD1QCaJTW7sEBhQQVNLe5'
            ]
        ]));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/externalcontact/group_welcome_template/get', [
            'template_id' => 'msg2MgBEgAATurBYDPgS32DfSt5vdzaHA'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('msg2MgBEgAATurBYDPgS32DfSt5vdzaHA'));
    }

    public function testDel()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/externalcontact/group_welcome_template/del', [
            'template_id' => 'msg2MgBEgAATurBYDPgS32DfSt5vdzaHA'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->del('msg2MgBEgAATurBYDPgS32DfSt5vdzaHA'));
    }
}
