<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MicroMerchant\Withdraw;

use EasyWeChat\MicroMerchant\Application;
use EasyWeChat\MicroMerchant\Withdraw\Client;
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

    public function testQueryWithdrawalStatus()
    {
        $client = $this->mockApiClient(Client::class, ['queryWithdrawalStatus'], $this->getApp())->makePartial();
        $date = '20180508';
        $sub_mch_id = '1900000109';
        $client->expects()->queryWithdrawalStatus($date, $sub_mch_id)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->queryWithdrawalStatus($date, $sub_mch_id));
    }

    public function testReAutoWithdrawByDate()
    {
        $client = $this->mockApiClient(Client::class, ['requestWithdraw'], $this->getApp())->makePartial();
        $date = '20180508';
        $sub_mch_id = '1900000109';
        $client->expects()->requestWithdraw($date, $sub_mch_id)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->requestWithdraw($date, $sub_mch_id));
    }
}
