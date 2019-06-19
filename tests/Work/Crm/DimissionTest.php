<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Crm;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Crm\DimissionClient;

class DimissionTest extends TestCase
{
    public function testGetUnassignedList()
    {
        $client = $this->mockApiClient(DimissionClient::class);

        $params = [
            'page_id' => 1,
            'page_size' => 1000,
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_unassigned_list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUnassignedList(1));
    }

    public function testTransfer()
    {
        $client = $this->mockApiClient(DimissionClient::class);

        $params = [
            'external_userid' => 'mock-external-userid',
            'handover_userid' => 'mock-handover-userid',
            'takeover_userid' => 'mock-takeover-userid',
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/transfer', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->transfer('mock-external-userid', 'mock-handover-userid', 'mock-takeover-userid'));
    }
}
