<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\OfficialAccount\Account;

use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Account\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetPreAuthorizationUrl()
    {
        $app = new Application(['app_id' => 'component-app-id']);

        $client = \Mockery::mock(Client::class.'[request]', [new Application(['app_id' => 'app-id']), $app]);

        $this->assertSame(
            'https://mp.weixin.qq.com/cgi-bin/fastregisterauth?copy_wx_verify=0&component_appid=component-app-id&appid=app-id&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback',
            $client->getFastRegistrationUrl('https://easywechat.com/callback', false)
        );
    }

    public function testRegister()
    {
        $app = new Application(['app_id' => 'component-app-id']);

        $client = \Mockery::mock(Client::class.'[httpPostJson]', [new Application(['app_id' => 'app-id']), $app]);
        $client->expects()->httpPostJson('cgi-bin/account/fastregister', [
            'ticket' => 'ticket',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->register('ticket'));
    }
}
