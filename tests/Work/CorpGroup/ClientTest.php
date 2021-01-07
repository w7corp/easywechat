<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\CorpGroup;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\Work\CorpGroup\Client;
use EasyWeChat\Tests\TestCase;

/**
 * Class ClientTest.
 */
class ClientTest extends TestCase
{
    public function testGetAppShareInfo()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'agentid' => 100001
        ];

        $client->expects()->httpPostJson('cgi-bin/corpgroup/corp/list_app_share_info', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAppShareInfo(100001));
    }

    public function testGetToken()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'corpid' => 'wwd216fa8c4c5c0e7x',
            'agentid' => 100001
        ];

        $client->expects()->httpPostJson('cgi-bin/corpgroup/corp/gettoken', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getToken('wwd216fa8c4c5c0e7x',100001));
    }

    public function testGetMiniProgramTransferSession()
    {
        $client = $this->mockApiClient(Client::class);

        $params = [
            'userid' => 'wmAoNVCwAAUrSqEqz7oQpEIEMVWDrPeg',
            'session_key' => 'n8cnNEoyW1pxSRz6/Lwjwg=='
        ];

        $client->expects()->httpPostJson('cgi-bin/miniprogram/transfer_session', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getMiniProgramTransferSession('wmAoNVCwAAUrSqEqz7oQpEIEMVWDrPeg','n8cnNEoyW1pxSRz6/Lwjwg=='));
    }
}
