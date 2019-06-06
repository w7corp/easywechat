<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant\Base;

use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Base\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function getApp()
    {
        return new Application([
            'mch_id' => 'mock-mch_id',
            'key' => 'mock-key123456789101234567891011',
        ]);
    }

    public function testApplyEnter()
    {
        $client = $this->mockApiClient(Client::class, ['submitApplication'], $this->getApp())->makePartial();
        $params = [
            'business_code' => '122222',
            'id_card_copy' => '111',
            // ...
        ];
        $client->expects()->submitApplication($params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->submitApplication($params));
    }

    public function testGetState()
    {
        $client = $this->mockApiClient(Client::class, ['getStatus'], $this->getApp())->makePartial();
        $applymentId = 'applyment_id';
        $client->expects()->getStatus($applymentId)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getStatus($applymentId));
    }

    public function testUpgrade()
    {
        $client = $this->mockApiClient(Client::class, ['upgrade'], $this->getApp())->makePartial();
        $params = [
            'sub_mch_id' => 'sub_mch_id',
            'organization_type' => 2,
            'business_license_copy' => '2ewajkfjskdjfi3ji4jf93wi4j3438348932nnd',
            // ...
        ];
        $client->expects()->upgrade($params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->upgrade($params));
    }

    public function testGetUpgradeState()
    {
        $client = $this->mockApiClient(Client::class, ['getUpgradeStatus'], $this->getApp())->makePartial();
        $client->expects()->getUpgradeStatus('sub_mch_id')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getUpgradeStatus('sub_mch_id'));
    }
}
