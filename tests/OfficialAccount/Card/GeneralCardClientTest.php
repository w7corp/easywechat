<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Card;

use EasyWeChat\OfficialAccount\Card\GeneralCardClient;
use EasyWeChat\Tests\TestCase;

class GeneralCardClientTest extends TestCase
{
    public function testActivate()
    {
        $client = $this->mockApiClient(GeneralCardClient::class);

        $params = [
            'foo' => 'bar',
        ];
        $client->expects()->httpPostJson('card/generalcard/activate', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->activate($params));
    }

    public function testDeactivate()
    {
        $client = $this->mockApiClient(GeneralCardClient::class);

        $params = [
            'card_id' => 'mock-card-id',
            'code' => 'bar',
        ];
        $client->expects()->httpPostJson('card/generalcard/unactivate', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->deactivate('mock-card-id', 'bar'));
    }

    public function testUpdateUser()
    {
        $client = $this->mockApiClient(GeneralCardClient::class);

        $client->expects()->httpPostJson('card/generalcard/updateuser', ['foo' => 'bar'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->updateUser(['foo' => 'bar']));
    }
}
