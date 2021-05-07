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

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Application;
use EasyWeChat\OpenPlatform\Authorizer\Auth\AccessToken as AuthorizerAccessToken;
use EasyWeChat\Tests\TestCase;
use Psr\SimpleCache\CacheInterface;

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
            'https://mp.weixin.qq.com/safe/bindcomponent?auth_type=3&pre_auth_code=auth-code%40123456&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback&action=bindcomponent&no_scan=1#wechat_redirect',
            $app->getMobilePreAuthorizationUrl('https://easywechat.com/callback')
        );

        $this->assertSame(
            'https://mp.weixin.qq.com/safe/bindcomponent?auth_type=3&pre_auth_code=auth-code%40123456&redirect_uri=https%3A%2F%2Feasywechat.com%2Fcallback&action=bindcomponent&no_scan=1#wechat_redirect',
            $app->getMobilePreAuthorizationUrl('https://easywechat.com/callback', 'auth-code@123456')
        );
    }

    public function testOfficialAccount()
    {
        $config = [
            'app_id' => 'component-app-id',
            'secret' => 'component-secret',
            'token' => 'component-token',
            'aes_key' => 'Qqx2S6jV3mp5prWPg5x3eBmeU1kLayZio4Q9ZxWTbmf',
            'debug' => true,
            'response_type' => 'collection',
            'log' => [
                'level' => 'debug',
                'permission' => 0777,
                'file' => '/tmp/easywechat.log',
            ],
        ];

        $app = \Mockery::mock(
            Application::class.'[getAuthorizerConfig]',
            [$config],
            function ($mock) use ($config) {
                $mock->shouldAllowMockingProtectedMethods();
                $mock->allows()->getAuthorizerConfig()->with('app-id', 'refresh-token')->andReturn(\array_merge($config, [
                    'component_app_id' => 'component-app-id',
                    'component_app_token' => 'app-token',
                    'app_id' => 'app-id',
                    'refresh_token' => 'refresh-token',
                ]));
            });

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
        $app = new Application([
            'app_id' => 'component-app-id',
            'secret' => 'component-secret',
            'token' => 'component-token',
            'aes_key' => 'Qqx2S6jV3mp5prWPg5x3eBmeU1kLayZio4Q9ZxWTbmf'
        ]);

        $cache = \Mockery::mock(CacheInterface::class);

        $token = \Mockery::mock(
            \EasyWeChat\Kernel\AccessToken::class.'[getCacheKey,getCache,requestToken,setToken,getCredentials]',
            [new ServiceContainer()]
        )->shouldAllowMockingProtectedMethods();

        $token->allows()->getCredentials()->andReturn([
            'foo' => 'foo',
            'bar' => 'bar',
        ]);
        $token->allows()->getCacheKey()->andReturn('mock-cache-key');
        $token->allows()->getCache()->andReturn($cache);

        $cache->expects()->get('mock-cache-key')->andReturn([
            'component_access_token' => 'component-token',
            'expires_in' => 7200,
        ])->times(2);

        $app['access_token'] = $token;

        $officialAccount = $app->officialAccount('app-id', 'refresh-token');

        $this->assertInstanceOf('Overtrue\Socialite\Providers\WeChat', $officialAccount->oauth);
    }

    public function testMiniProgram()
    {
        $app = new Application(['app_id' => 'component-app-id', 'secret' => 'component-secret', 'token' => 'component-token', 'aes_key' => 'Qqx2S6jV3mp5prWPg5x3eBmeU1kLayZio4Q9ZxWTbmf']);

        $cache = \Mockery::mock(CacheInterface::class);

        $token = \Mockery::mock(
            \EasyWeChat\Kernel\AccessToken::class.'[getCacheKey,getCache,requestToken,setToken,getCredentials]',
            [new ServiceContainer()]
        )->shouldAllowMockingProtectedMethods();

        $token->allows()->getCredentials()->andReturn([
            'foo' => 'foo',
            'bar' => 'bar',
        ]);
        $token->allows()->getCacheKey()->andReturn('mock-cache-key');
        $token->allows()->getCache()->andReturn($cache);

        $cache->expects()->get('mock-cache-key')->andReturn([
            'component_access_token' => 'component-token',
            'expires_in' => 7200,
        ]);

        $app['access_token'] = $token;

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
