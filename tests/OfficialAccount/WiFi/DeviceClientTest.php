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

use EasyWeChat\OfficialAccount\WiFi\DeviceClient;
use EasyWeChat\Tests\TestCase;

class DeviceClientTest extends TestCase
{
    public function testAddPasswordDevice()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()
            ->httpPostJson('bizwifi/device/add', [
                'shop_id' => 100,
                'ssid' => 'mock-ssid',
                'password' => 'mock-password',
            ])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addPasswordDevice(100, 'mock-ssid', 'mock-password'));
    }

    public function testAddPortalDevice()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()
            ->httpPostJson('bizwifi/apportal/register', [
                'shop_id' => 100,
                'ssid' => 'mock-ssid',
                'reset' => true,
            ])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->addPortalDevice(100, 'mock-ssid', true));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()
            ->httpPostJson('bizwifi/device/delete', ['bssid' => 'mock-bssid'])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete('mock-bssid'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()
            ->httpPostJson('bizwifi/device/list', [
                'pageindex' => 1,
                'pagesize' => 20,
            ])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list(1, 20));
    }

    public function testListByShopId()
    {
        $client = $this->mockApiClient(DeviceClient::class);

        $client->expects()
            ->httpPostJson('bizwifi/device/list', [
                'shop_id' => 100,
                'pageindex' => 1,
                'pagesize' => 20,
            ])
            ->andReturn('mock-result');

        $this->assertSame('mock-result', $client->listByShopId(100, 1, 20));
    }
}
