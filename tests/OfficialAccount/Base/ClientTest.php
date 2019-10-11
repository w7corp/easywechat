<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Base;

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OfficialAccount\Base\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testClearQuota()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('cgi-bin/clear_quota', [
            'appid' => '123456',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->clearQuota());
    }

    public function testGetValidIps()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/getcallbackip')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getValidIps());
    }

    public function testCheckCallbackUrl()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/callback/check', [
            'action' => 'all',
            'check_operator' => 'DEFAULT',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->checkCallbackUrl());

        try {
            $client->checkCallbackUrl('invalid-action');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('The action must be dns, ping, all.', $e->getMessage());
        }

        try {
            $client->checkCallbackUrl('all', 'invalid-operator');
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('The operator must be CHINANET, UNICOM, CAP, DEFAULT.', $e->getMessage());
        }
    }
}
