<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant\Material;

use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Material\Client;
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

    public function testModifyArchives()
    {
        $client = $this->mockApiClient(Client::class, ['setSettlementCard'], $this->getApp())->makePartial();
        $params = [
            'account_number' => '122222',
            'bank_name' => '浙商银行xxx支行',
            // ...
        ];
        $client->expects()->setSettlementCard($params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->setSettlementCard($params));
    }

    public function testModifyContactInfo()
    {
        $client = $this->mockApiClient(Client::class, ['updateContact'], $this->getApp())->makePartial();
        $params = [
            'mobile_phone' => '13200000000',
            'email' => '13200000000@163.com',
            // ...
        ];
        $client->expects()->updateContact($params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->updateContact($params));
    }
}
