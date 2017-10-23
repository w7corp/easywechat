<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Jssdk;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Application;
use EasyWeChat\Work\Jssdk\Client;

class ClientTest extends TestCase
{
    public function testGetAppid()
    {
        $client = $this->mockApiClient(Client::class, ['getAppId'], new Application(['corp_id' => 'wx123']))->shouldAllowMockingProtectedMethods();
        $client->expects()->getAppId()->passthru();
        $this->assertSame('wx123', $client->getAppId());
    }
}
