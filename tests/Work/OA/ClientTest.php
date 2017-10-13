<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\OA;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\OA\Client;

class ClientTest extends TestCase
{
    public function testCheckinRecords()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/checkin/getcheckindata', [
            'opencheckindatatype' => 3,
            'starttime' => 1408272000,
            'endtime' => 1408274000,
            'useridlist' => ['overtrue', 'tianyong'],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->checkinRecords(1408272000, 1408274000, ['overtrue', 'tianyong']));
    }

    public function testApprovalRecords()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/corp/getapprovaldata', [
            'starttime' => 1408272000,
            'endtime' => 1408274000,
            'next_spnum' => 12,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->approvalRecords(1408272000, 1408274000, 12));
    }
}
