<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\OfficialAccount\OAuth;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\OAuth\ComponentDelegate;
use EasyWeChat\Tests\TestCase;

class ComponentDelegateTest extends TestCase
{
    public function testGetAppId()
    {
        $app = new Application(['app_id' => 'mock-app-id']);
        $delegate = new ComponentDelegate($app);

        $this->assertSame('mock-app-id', $delegate->getAppId());
    }

    public function testGetToken()
    {
        $app = new Application();
        $app['access_token'] = \Mockery::mock(\EasyWeChat\OpenPlatform\Auth\AccessToken::class.'[getToken]', [\Mockery::mock(ServiceContainer::class)], function ($mock) {
            $mock->expects()->getToken()->andReturn(['component_access_token' => 'mock-token']);
        });
        $delegate = new ComponentDelegate($app);

        $this->assertSame('mock-token', $delegate->getToken());
    }
}
