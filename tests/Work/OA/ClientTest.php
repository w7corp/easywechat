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

    public function testCheckinRules()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/checkin/getcheckinoption', [
            'day' => '2019-10-28',
            'useridlist' => ['overtrue', 'tianyong'],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->checkinRules('2019-10-28', ['overtrue', 'tianyong']));
    }

    public function testApprovalNumbers()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/oa/getapprovalinfo', [
            'starttime' => 1408272000,
            'endtime' => 1408274000,
            'cursor' => 0,
            'size' => 100,
            'filters'=>[]
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->approvalNumbers(1408272000, 1408274000, 0, 100, []));
    }

    public function testApprovalDetail()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/oa/getapprovaldetail', [
            'sp_no' => 201910280001,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->approvalDetail(201910280001));
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
