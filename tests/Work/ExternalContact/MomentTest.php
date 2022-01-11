<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\ExternalContact;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\ExternalContact\MomentClient;

class MomentTest extends TestCase
{
    public function testCreateTask(): void
    {
        $client = $this->mockApiClient(MomentClient::class);

        $params = [
            'text' => [
                'content' => '如果你能看到这个朋友圈，免费领取5元红包！'
            ],
            'attachments' => [
                [
                    'msgtype' => 'image',
                    'image' => [
                        'media_id' => 'WWCISP_nNVkm2XJNRKy_pDYT-2RK3T-qd1N_9fPuq9UpAYgF8eyhFC076wNbZdgWgMoams7geBU5wkWFzulNaODakaIxBlIHIRS9OYsbhz2DbBclO6sonUl8Cf2Xbih04DwYCaz20KQE3v2IU2XqRPuaM0nX22bYfeZRD1Q7171lZ0QIftlzJTfZvLFTxFhPA1rAZZrBpJelM7_5vRgNBHjgqLFJ5MjaJIxhFZHplBYui7QXmzKreupgItQT-sbGXZqAXBSEyO_sMrXW7ghFQtpPziRlv1KHRzxuiGRwi8IU4QmEORK6N1zKlkxex9FKZxJus0L3TLiG7eEi-mYDJskuhHuXQ'
                    ]
                ]
            ],
            'visible_range' => [
                'sender_list' => [
                    'user_list' => [
                        'HaoLiang'
                    ]
                ]
            ]
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/add_moment_task', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->createTask($params));
    }

    public function testGetTask(): void
    {
        $client = $this->mockApiClient(MomentClient::class);

        $jobId = 'UsqkaVyEEV4_Ep5xHLllO9Lr38FTm3AhlO0wthHAQ_o';

        $client->expects()->httpGet('cgi-bin/externalcontact/get_moment_task_result', ['jobid' => $jobId])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getTask($jobId));
    }

    public function testList(): void
    {
        $client = $this->mockApiClient(MomentClient::class);

        $params = [
            'start_time' => 1605000000,
            'end_time' => 1605172726,
            'creator' => 'zhangshan',
            'filter_type' => 1,
            'cursor' => 'CURSOR',
            'limit' => 10
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_moment_list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list($params));
    }

    public function testTask(): void
    {
        $client = $this->mockApiClient(MomentClient::class);

        $params = [
            'moment_id' => 'momxxx',
            'cursor' => 'CURSOR',
            'limit' => 10
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_moment_task', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getTasks('momxxx', 'CURSOR', 10));
    }

    public function testCustomers(): void
    {
        $client = $this->mockApiClient(MomentClient::class);

        $params = [
            'moment_id' => 'momxxx',
            'userid' => 'xxx',
            'cursor' => 'CURSOR',
            'limit' => 10
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_moment_customer_list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCustomers('momxxx', 'xxx', 'CURSOR', 10));
    }

    public function testSendResult(): void
    {
        $client = $this->mockApiClient(MomentClient::class);

        $params = [
            'moment_id' => 'momxxx',
            'userid' => 'xxx',
            'cursor' => 'CURSOR',
            'limit' => 10
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_moment_send_result', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getSendResult('momxxx', 'xxx', 'CURSOR', 10));
    }

    public function testComments(): void
    {
        $client = $this->mockApiClient(MomentClient::class);

        $params = [
            'moment_id' => 'momxxx',
            'userid' => 'xxx'
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_moment_comments', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getComments('momxxx', 'xxx'));
    }
}
