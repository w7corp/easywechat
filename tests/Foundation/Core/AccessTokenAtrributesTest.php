<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Foundation\Core;

use EasyWeChat\Tests\TestCase;
use Mockery as m;

class AccessTokenAtrributesTest extends TestCase
{
    protected function getMockHttp($tokenJsonKey = 'access_token')
    {
        return m::mock('EasyWeChat\OfficialAccount\Core\Http[parseJSON,get]', function ($mock) use ($tokenJsonKey) {
            $mock->shouldReceive('parseJSON')->andReturnUsing(function ($requests) use ($tokenJsonKey) {
                return array_merge([
                    $tokenJsonKey => 'thisIsAToken',
                    'expires_in' => 7200,
                ], $requests);
            });
            $mock->shouldReceive('get')->andReturnUsing(function ($endpoint, $params) {
                return compact('endpoint', 'params');
            });
        });
    }

    protected function getMockCache()
    {
        return m::mock('Doctrine\Common\Cache\Cache', function ($mock) {
            $mock->shouldReceive('fetch')->andReturn('thisIsAToken');
        });
    }

    public function getOfficialAccount(...$args)
    {
        $instance = new \EasyWeChat\OfficialAccount\Core\AccessToken(...$args);

        return $instance->setHttp($this->getMockHttp())->setCache($this->getMockCache());
    }

    public function getMiniProgram(...$args)
    {
        $instance = new \EasyWeChat\MiniProgram\AccessToken(...$args);

        return $instance->setHttp($this->getMockHttp())->setCache($this->getMockCache());
    }

    public function getOpenPlatform(...$args)
    {
        $instance = new \EasyWeChat\OpenPlatform\AccessToken(...$args);

        $verifyTicket = new \EasyWeChat\OpenPlatform\VerifyTicket('appid', new \Doctrine\Common\Cache\ArrayCache());
        $verifyTicket->setTicket('ticket@foobar');

        return $instance->setHttp($this->getMockHttp('component_access_token'))->setCache($this->getMockCache())->setVerifyTicket($verifyTicket);
    }

    public function testClientIdAndClientSecret()
    {
        $officialAccount = $this->getOfficialAccount('app-id', 'app-secret');

        $this->assertEquals('app-id', $officialAccount->getClientId());
        $this->assertEquals('app-secret', $officialAccount->getClientSecret());

        $miniProgram = $this->getMiniProgram('mini-app-id', 'mini-secret');

        $this->assertEquals('mini-app-id', $miniProgram->getClientId());
        $this->assertEquals('mini-secret', $miniProgram->getClientSecret());

        $openPlatform = $this->getOpenPlatform('open-app-id', 'open-secret');

        $this->assertEquals('open-app-id', $openPlatform->getClientId());
        $this->assertEquals('open-secret', $openPlatform->getClientSecret());
    }

    public function testGetQueryName()
    {
        $officialAccount = $this->getOfficialAccount('app-id', 'app-secret');
        $miniProgram = $this->getMiniProgram('mini-app-id', 'mini-secret');
        $openPlatform = $this->getOpenPlatform('open-app-id', 'open-secret');

        $this->assertEquals('access_token', $officialAccount->getQueryName());
        $this->assertEquals('access_token', $miniProgram->getQueryName());
        $this->assertEquals('component_access_token', $openPlatform->getQueryName());
    }

    public function testGetQueryFields()
    {
        $officialAccount = $this->getOfficialAccount('app-id', 'app-secret');
        $miniProgram = $this->getMiniProgram('mini-app-id', 'mini-secret');
        $openPlatform = $this->getOpenPlatform('open-app-id', 'open-secret');

        $this->assertSame(['access_token' => 'thisIsAToken'], $officialAccount->getQueryFields());
        $this->assertSame(['access_token' => 'thisIsAToken'], $miniProgram->getQueryFields());
        $this->assertSame(['component_access_token' => 'thisIsAToken'], $openPlatform->getQueryFields());
    }

    public function testGetCacheKey()
    {
        $officialAccount = $this->getOfficialAccount('app-id', 'app-secret');
        $miniProgram = $this->getMiniProgram('mini-app-id', 'mini-secret');
        $openPlatform = $this->getOpenPlatform('open-app-id', 'open-secret');

        $this->assertEquals('easywechat.common.access_token.app-id', $officialAccount->getCacheKey());
        $this->assertEquals('easywechat.common.mini.program.access_token.mini-app-id', $miniProgram->getCacheKey());
        $this->assertEquals('easywechat.open_platform.component_access_token.open-app-id', $openPlatform->getCacheKey());
    }

    public function testGetTokenFromServer()
    {
        $officialAccountResult = $this->getOfficialAccount('app-id', 'app-secret')->getTokenFromServer();
        $miniProgramResult = $this->getMiniProgram('mini-app-id', 'mini-secret')->getTokenFromServer();
        $openPlatformResult = $this->getOpenPlatform('open-app-id', 'open-secret')->getTokenFromServer();

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/token', $officialAccountResult['endpoint']);
        $this->assertSame([
                'appid' => 'app-id',
                'secret' => 'app-secret',
                'grant_type' => 'client_credential',
            ], $officialAccountResult['params']
        );

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/token', $miniProgramResult['endpoint']);
        $this->assertSame([
                'appid' => 'mini-app-id',
                'secret' => 'mini-secret',
                'grant_type' => 'client_credential',
            ], $miniProgramResult['params']
        );

        $this->assertEquals('https://api.weixin.qq.com/cgi-bin/component/api_component_token', $openPlatformResult['endpoint']);
        $this->assertSame([
                'component_appid' => 'open-app-id',
                'component_appsecret' => 'open-secret',
                'component_verify_ticket' => 'ticket@foobar',
            ], $openPlatformResult['params']
        );
    }
}
