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

use EasyWeChat\MicroMerchant\Base\Client;
use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function getApp()
    {
        return new Application([
            'mch_id' => 'mock-mch_id',
            'key'    => 'mock-key123456789101234567891011',
        ]);
    }

    public function testApplyEnter()
    {
        $client = $this->mockApiClient(Client::class, ['applyForEnter'], $this->getApp())->makePartial();
        $params = [
            'business_code' => '122222',
            'id_card_copy'  => '111',
            // ...
        ];
        $client->expects()->applyForEnter($params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->applyForEnter($params));
    }

    public function testGetState()
    {
        $client       = $this->mockApiClient(Client::class, ['getState'], $this->getApp())->makePartial();
        $applyment_id = 'applyment_id';
        $client->expects()->getState($applyment_id)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getState($applyment_id));
    }

    public function testUpgrade()
    {
        $client = $this->mockApiClient(Client::class, ['upgrade'], $this->getApp())->makePartial();
        $params = [
            'sub_mch_id'            => 'sub_mch_id',
            'organization_type'     => 2,
            'business_license_copy' => '2ewajkfjskdjfi3ji4jf93wi4j3438348932nnd',
            // ...
        ];
        $client->expects()->upgrade($params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->upgrade($params));
    }


    public function testGetUpgradeState()
    {
        $client = $this->mockApiClient(Client::class, ['getUpgradeState'], $this->getApp())->makePartial();
        $client->expects()->getUpgradeState('sub_mch_id')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getUpgradeState('sub_mch_id'));
    }
}
