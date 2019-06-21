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
use EasyWeChat\Work\ExternalContact\ContactWayClient;

class ContactWayTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(ContactWayClient::class);

        $params = [
            'type' => 1,
            'scene' => 2,
            'style' => 1,
            'state' => 'test-state',
            'user' => ['UserID1', 'UserID2', 'UserID3'],
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/add_contact_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create(1, 2, [
            'style' => 1,
            'state' => 'test-state',
            'user' => ['UserID1', 'UserID2', 'UserID3'],
        ]));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(ContactWayClient::class);

        $configId = '42b34949e138eb6e027c123cba77fad7';
        $params = [
            'config_id' => $configId,
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/get_contact_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get($configId));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(ContactWayClient::class);

        $configId = '42b34949e138eb6e027c123cba77fad7';
        $params = [
            'config_id' => $configId,
            'style' => 1,
            'state' => 'test-state',
            'user' => ['UserID1', 'UserID2', 'UserID3'],
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/update_contact_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update($configId, [
            'style' => 1,
            'state' => 'test-state',
            'user' => ['UserID1', 'UserID2', 'UserID3'],
        ]));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(ContactWayClient::class);

        $configId = '42b34949e138eb6e027c123cba77fad7';
        $params = [
            'config_id' => $configId,
        ];

        $client->expects()->httpPostJson('cgi-bin/externalcontact/del_contact_way', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete($configId));
    }
}
