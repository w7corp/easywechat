<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform;

use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorizer\Auth\AccessToken as AuthorizerAccessToken;
use EasyWeChat\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testProperties()
    {
        $app = new Application(['app_id' => 'app-id']);

        $this->assertInstanceOf(\EasyWeChat\OpenPlatform\Auth\AccessToken::class, $app->access_token);
        $this->assertInstanceOf(\EasyWeChat\OpenPlatform\Auth\VerifyTicket::class, $app->verify_ticket);
        $this->assertInstanceOf(\EasyWeChat\OpenPlatform\Server\Guard::class, $app->server);
        $this->assertInstanceOf(\EasyWeChat\OpenPlatform\CodeTemplate\Client::class, $app->code_template);
        $this->assertInstanceOf(\EasyWeChat\OpenPlatform\Component\Client::class, $app->component);
    }

    public function testGetPreAuthorizationUrl()
    {
        $app = \Mockery::mock(Application::class.'[createPreAuthorizationCode]', ['app_id' => 'component-app-id'], function ($mock) {
            $mock->expects()->createPreAuthorizationCode()->andReturn([
                'pre_auth_code' => 'auth-code@123456',
            ]);
        });

        $this->assertSame(
            'https://mp.weixin.qq.com/cgi-bin/componentloginpage?pre_auth_code=auth-code%40123456&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback',
            $app->getPreAuthorizationUrl('https://easywechat.com/callback')
        );

        $this->assertSame(
            'https://mp.weixin.qq.com/cgi-bin/componentloginpage?pre_auth_code=auth-code%40123456&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback',
            $app->getPreAuthorizationUrl('https://easywechat.com/callback', 'auth-code@123456')
        );
    }

    public function testGetMobilePreAuthorizationUrl()
    {
        $app = \Mockery::mock(Application::class.'[createPreAuthorizationCode]', ['app_id' => 'component-app-id'], function ($mock) {
            $mock->expects()->createPreAuthorizationCode()->andReturn([
                'pre_auth_code' => 'auth-code@123456',
            ]);
        });

        $this->assertSame(
            'https://mp.weixin.qq.com/safe/bindcomponent?pre_auth_code=auth-code%40123456&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback&action=bindcomponent&no_scan=1#wechat_redirect',
            $app->getMobilePreAuthorizationUrl('https://easywechat.com/callback')
        );

        $this->assertSame(
            'https://mp.weixin.qq.com/safe/bindcomponent?pre_auth_code=auth-code%40123456&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback&action=bindcomponent&no_scan=1#wechat_redirect',
            $app->getMobilePreAuthorizationUrl('https://easywechat.com/callback', 'auth-code@123456')
        );
    }

    public function testOfficialAccount()
    {
        $app = new Application([
            'app_id' => 'component-app-id', 'secret' => 'component-secret',
            'token' => 'component-token', 'aes_key' => 'Qqx2S6jV3mp5prWPg5x3eBmeU1kLayZio4Q9ZxWTbmf',
            'debug' => true,
            'response_type' => 'collection',
            'log' => [
                'level' => 'debug',
                'permission' => 0777,
                'file' => '/tmp/easywechat.log',
            ],
        ]);
        $officialAccount = $app->officialAccount('app-id', 'refresh-token');

        $this->assertInstanceOf('EasyWeChat\OfficialAccount\Application', $officialAccount);
        $this->assertInstanceOf(AuthorizerAccessToken::class, $officialAccount['access_token']);
        $this->assertInstanceOf(\EasyWeChat\OpenPlatform\Authorizer\Server\Guard::class, $officialAccount['server']);
        $this->assertInstanceOf(\EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\Account\Client::class, $officialAccount['account']);

        $this->assertArraySubset([
            'debug' => true,
            'response_type' => 'collection',
            'log' => [
                'level' => 'debug',
                'permission' => 0777,
                'file' => '/tmp/easywechat.log',
            ],
            'app_id' => 'app-id',
            'refresh_token' => 'refresh-token',
        ], $officialAccount->config->toArray());
    }

    public function testOfficialAccountOAuth()
    {
        $app = new Application(['app_id' => 'component-app-id', 'secret' => 'component-secret', 'token' => 'component-token', 'aes_key' => 'Qqx2S6jV3mp5prWPg5x3eBmeU1kLayZio4Q9ZxWTbmf']);
        $officialAccount = $app->officialAccount('app-id', 'refresh-token');

        $this->assertInstanceOf('Overtrue\Socialite\Providers\WeChat', $officialAccount->oauth);
    }

    public function testMiniProgram()
    {
        $app = new Application(['app_id' => 'component-app-id', 'secret' => 'component-secret', 'token' => 'component-token', 'aes_key' => 'Qqx2S6jV3mp5prWPg5x3eBmeU1kLayZio4Q9ZxWTbmf']);
        $miniProgram = $app->miniProgram('app-id', 'refresh-token');

        $this->assertInstanceOf('EasyWeChat\MiniProgram\Application', $miniProgram);
        $this->assertInstanceOf('\EasyWeChat\MiniProgram\Encryptor', $miniProgram->encryptor);
        $this->assertInstanceOf('\EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Auth\Client', $miniProgram->auth);
    }

    public function testDynamicCalls()
    {
        $app = new Application(['app_id' => 'component-app-id', 'secret' => 'component-secret', 'token' => 'component-token', 'aes_key' => 'Qqx2S6jV3mp5prWPg5x3eBmeU1kLayZio4Q9ZxWTbmf']);
        $app['base'] = new class() {
            public function dummyMethod()
            {
                return 'mock-result';
            }
        };

        $this->assertSame('mock-result', $app->dummyMethod());
    }
}
