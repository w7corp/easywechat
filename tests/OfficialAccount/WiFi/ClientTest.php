<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\WiFi;

use EasyWeChat\OfficialAccount\WiFi\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testSummary()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('bizwifi/statistics/list', [
                'begin_date' => 'mock-begin-date',
                'end_date' => 'mock-end-date',
                'shop_id' => 100,
            ])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->summary('mock-begin-date', 'mock-end-date', 100));
    }

    public function testGetQrCodeUrl()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('bizwifi/qrcode/get', [
                'shop_id' => 100,
                'ssid' => 'mock-ssid',
                'img_id' => 1,
            ])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getQrCodeUrl(100, 'mock-ssid', 1));
    }

    public function testSetFinishPage()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('bizwifi/finishpage/set', ['foo' => 'bar'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setFinishPage(['foo' => 'bar']));
    }

    public function testSetHomePage()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()
            ->httpPostJson('bizwifi/homepage/set', ['foo' => 'bar'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setHomePage(['foo' => 'bar']));
    }
}
