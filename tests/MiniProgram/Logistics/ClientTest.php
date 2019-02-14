<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\Logistics;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\MiniProgram\Logistics\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{

    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/express/business/delivery/getall')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->list(5, 10));
    }

}
