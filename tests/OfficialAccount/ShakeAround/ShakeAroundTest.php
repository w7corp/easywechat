<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\ShakeAround;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\OfficialAccount\Application;
use EasyWeChat\OfficialAccount\ShakeAround\Client;
use EasyWeChat\OfficialAccount\ShakeAround\DeviceClient;
use EasyWeChat\OfficialAccount\ShakeAround\GroupClient;
use EasyWeChat\OfficialAccount\ShakeAround\MaterialClient;
use EasyWeChat\OfficialAccount\ShakeAround\RelationClient;
use EasyWeChat\OfficialAccount\ShakeAround\ShakeAround;
use EasyWeChat\OfficialAccount\ShakeAround\StatsClient;
use EasyWeChat\Tests\TestCase;

class ShakeAroundTest extends TestCase
{
    public function testInstances()
    {
        $app = new Application();
        $shakeAround = new ShakeAround($app);

        $this->assertInstanceOf(Client::class, $shakeAround);
        $this->assertInstanceOf(DeviceClient::class, $shakeAround->device);
        $this->assertInstanceOf(GroupClient::class, $shakeAround->group);
        $this->assertInstanceOf(MaterialClient::class, $shakeAround->material);
        $this->assertInstanceOf(RelationClient::class, $shakeAround->relation);
        $this->assertInstanceOf(StatsClient::class, $shakeAround->stats);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No shake_around service named "foo".', $shakeAround->foo);
    }
}
