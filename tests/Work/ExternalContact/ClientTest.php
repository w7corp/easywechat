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
use EasyWeChat\Work\ExternalContact\Client;

class ClientTest extends TestCase
{
    public function testGetExternalContact()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/externalcontact/get', ['external_userid' => 'mock-userid'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->get('mock-userid'));
    }

    public function testListExternalContact()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/externalcontact/list', ['userid' => 'mock-userid'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list('mock-userid'));
    }

    public function testGetFollowUsers()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/externalcontact/get_follow_user_list')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getFollowUsers());
    }

    public function testGetUnassigned()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'page_id' => 1,
            'page_size' => 1000,
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_unassigned_list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUnassigned(1));
    }

    public function testTransfer()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'external_userid' => 'mock-external-userid',
            'handover_userid' => 'mock-handover-userid',
            'takeover_userid' => 'mock-takeover-userid',
        ];
        $client->expects()->httpPostJson('cgi-bin/externalcontact/transfer', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->transfer('mock-external-userid', 'mock-handover-userid', 'mock-takeover-userid'));
    }
}
