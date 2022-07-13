<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Wedrive;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Wedrive\Client;

class ClientTest extends TestCase
{
    public function testProInfo()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/wedrive/mng_pro_info', ['userid' => 'test'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->proinfo('test'));
    }

    public function testCapacity()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/wedrive/mng_capacity')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->capacity());
    }
}
