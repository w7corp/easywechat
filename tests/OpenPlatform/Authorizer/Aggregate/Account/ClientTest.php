<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\Aggregate\Account;

use EasyWeChat\OpenPlatform\Authorizer\Aggregate\Account\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('create', ['appid' => 'app-id'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->create('app-id'));
    }

    public function testBind()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('bind', ['appid' => 'app-id', 'open_appid' => 'open-app-id'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->bind('app-id', 'open-app-id'));
    }

    public function testUnbind()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('unbind', ['appid' => 'app-id', 'open_appid' => 'open-app-id'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->unbind('app-id', 'open-app-id'));
    }

    public function testGetBinding()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('get', ['appid' => 'app-id'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->getBinding('app-id'));
    }
}
