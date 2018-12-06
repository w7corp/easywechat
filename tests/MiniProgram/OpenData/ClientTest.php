<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\MiniProgram\OpenData;

use EasyWeChat\MiniProgram\OpenData\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testRemoveUserStorage()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'key' => ['mock-key'],
        ];
        $client->expects()->httpPostJson('remove_user_storage', $data, [
            'openid' => 'mock-openid',
            'sig_method' => 'hmac_sha256',
            'signature' => hash_hmac('sha256', json_encode($data), 'mock-session-key'),
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->removeUserStorage('mock-openid', 'mock-session-key', ['mock-key']));
    }

    public function testSetUserStorage()
    {
        $client = $this->mockApiClient(Client::class);

        $data = [
            'kv_list' => [
                [
                    'key' => 'mock-key-1',
                    'value' => 'mock-value-1',
                ],
                [
                    'key' => 'mock-key-2',
                    'value' => 'mock-value-2',
                ],
            ],
        ];
        $client->expects()->httpPostJson('set_user_storage', $data, [
            'openid' => 'mock-openid',
            'sig_method' => 'hmac_sha256',
            'signature' => hash_hmac('sha256', json_encode($data), 'mock-session-key'),
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setUserStorage('mock-openid', 'mock-session-key', [
            'mock-key-1' => 'mock-value-1',
            'mock-key-2' => 'mock-value-2',
        ]));
    }
}
