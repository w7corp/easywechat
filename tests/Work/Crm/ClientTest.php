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
use EasyWeChat\Work\Crm\Client;

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

    public function testFollowUsers()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/externalcontact/get_follow_user_list')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->followUsers());
    }
}
