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
        $client->expects()->httpGet('cgi-bin/crm/get_external_contact', ['external_userid' => 'mock-userid'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->getExternalContact('mock-userid'));
    }
}
