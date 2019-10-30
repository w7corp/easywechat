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
            'datetime' => 1572192000,
            'useridlist' => ['overtrue', 'tianyong'],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->checkinRules(1572192000, ['overtrue', 'tianyong']));
    }

    public function testApprovalTemplate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/oa/gettemplatedetail', [
            'template_id' => 'ZLqk8pcsAoXZ1eY56vpAgfX28MPdYU3ayMaSPH',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->approvalTemplate('ZLqk8pcsAoXZ1eY56vpAgfX28MPdYU3ayMaSPH'));
    }

    public function testCreateApproval()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/oa/applyevent', [
            'creator_userid' => 'WangXiaoMing',
            'template_id' => '3Tka1eD6v6JfzhDMqPd3aMkFdxqtJMc2ZRioeFXk',
            'approver' => [
                [
                    'attr' => 1,
                    'userid' => ['LiuXiaoGang'],
                ],
            ],
            'apply_data' => [
                'contents' => [
                    [
                        'id' => 'Text-1569573760849',
                        'control' => 'Text',
                        'title' => [
                            'text' => '文本控件',
                            'lang' => 'zh_CN',
                        ],
                        'value' => [
                            'text' => '文本填写的内容',
                        ],
                    ],
                ],
            ],
            'summary_list' => [
                [
                    'summary_info' => [
                        'text' => '摘要第1行',
                        'lang' => 'zh_CN',
                    ],
                ],
                [
                    'summary_info' => [
                        'text' => '摘要第2行',
                        'lang' => 'zh_CN',
                    ],
                ],
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createApproval(['creator_userid' => 'WangXiaoMing', 'template_id' => '3Tka1eD6v6JfzhDMqPd3aMkFdxqtJMc2ZRioeFXk', 'approver' => [['attr' => 1, 'userid' => ['LiuXiaoGang']]], 'apply_data' => ['contents' => [['id' => 'Text-1569573760849', 'control' => 'Text', 'title' => ['text' => '文本控件', 'lang' => 'zh_CN'], 'value' => ['text' => '文本填写的内容']]]], 'summary_list' => [['summary_info' => ['text' => '摘要第1行', 'lang' => 'zh_CN']], ['summary_info' => ['text' => '摘要第2行', 'lang' => 'zh_CN']]]]));
    }

    public function testApprovalNumbers()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/oa/getapprovalinfo', [
            'starttime' => 1408272000,
            'endtime' => 1408274000,
            'cursor' => 0,
            'size' => 100,
            'filters' => [],
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
