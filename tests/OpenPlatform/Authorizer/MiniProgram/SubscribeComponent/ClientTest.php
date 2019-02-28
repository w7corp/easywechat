<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\MiniProgram\SubscribeComponent;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\SubscribeComponent\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGet()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpGet('wxa/getshowwxaitem')->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->get());
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/updateshowwxaitem', [
            'appid' => 'app-id',
            'wxa_subscribe_biz_flag' => 1,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->update('app-id', 1));
    }

    public function testGetAvailableList()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpGet('wxa/getwxamplinkforshow', [
            'page' => 0,
            'num' => 10,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->getAvailableList(0, 10));
    }
}
