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
