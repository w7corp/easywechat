<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Component;

use EasyWeChat\OpenPlatform\Component\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testRegisterMiniProgram()
    {
        $client = $this->mockApiClient(Client::class, []);

        $params = [
            'name' => 'aaa',
            'code' => '111',
            'code_type' => 1,
            'legal_persona_wechat' => 'aaa111',
            'legal_persona_name' => 'aaa111',
            'component_phone' => '111',
        ];

        $client->expects()->httpPostJson('cgi-bin/component/fastregisterweapp', $params, ['action' => 'create'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->registerMiniProgram($params));
    }

    public function testGetRegistrationStatus()
    {
        $client = $this->mockApiClient(Client::class, []);

        $params = [
            'name' => 'aaa',
            'legal_persona_wechat' => 'aaa111',
            'legal_persona_name' => 'aaa111',
        ];

        $client->expects()->httpPostJson('cgi-bin/component/fastregisterweapp', $params, ['action' => 'search'])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getRegistrationStatus('aaa', 'aaa111', 'aaa111'));
    }
}
