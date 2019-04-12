<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Base;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Base\Client;

class ClientTest extends TestCase
{
    public function testGetCallbackIp()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/getcallbackip')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getCallbackIp());
    }
}
