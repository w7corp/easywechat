<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenWork\Corp;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\Corp\Client;
use EasyWeChat\OpenWork\SuiteAuth\AccessToken;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetPreAuthorizationUrl()
    {
        $app = new ServiceContainer(['suite_id' => 'mock-suit-id']);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app);

        $expected = 'https://open.work.weixin.qq.com/3rdapp/install?suite_id=mock-suit-id&redirect_uri=mock-redirect-uri&pre_auth_code=mock-pre-auth-code&state=mock-state';
        $this->assertSame($expected, $client->getPreAuthorizationUrl('mock-pre-auth-code', 'mock-redirect-uri', 'mock-state'));
    }

    public function testGetPreAuthorizationUrlWithDefaultParam()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
            'redirect_uri_install' => 'mock-redirect-uri',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class.'[getPreAuthCode]', [], $app);
        $client->allows()->getPreAuthCode()->andReturn(['pre_auth_code' => 'mock-pre-auth-code']);

        $expected = 'https://open.work.weixin.qq.com/3rdapp/install?suite_id=mock-suit-id&redirect_uri=mock-redirect-uri&pre_auth_code=mock-pre-auth-code&state=mock-state';
        $this->assertSame($expected, $client->getPreAuthorizationUrl('', '', 'mock-state'));
    }

    public function testGetPreAuthCode()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, ['httpGet'], $app)->makePartial();
        $client->expects()->httpGet('cgi-bin/service/get_pre_auth_code')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getPreAuthCode());
    }

    public function testSetSession()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();
        $client->expects()->httpPostJson('cgi-bin/service/set_session_info', [
            'pre_auth_code' => 'mock-pre-auth-code',
            'session_info' => [
                'appid' => [1, 2, 3],
                'auth_type' => 1,
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setSession('mock-pre-auth-code', [
            'appid' => [1, 2, 3],
            'auth_type' => 1,
        ]));
    }

    public function testGetPermanentByCode()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();
        $client->expects()->httpPostJson('cgi-bin/service/get_permanent_code', [
            'auth_code' => 'mock-auth-code',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getPermanentByCode('mock-auth-code'));
    }

    public function testGetAuthorization()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();
        $client->expects()->httpPostJson('cgi-bin/service/get_auth_info', [
            'auth_corpid' => 'mock-auth-corp-id',
            'permanent_code' => 'mock-permanent-code',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getAuthorization('mock-auth-corp-id', 'mock-permanent-code'));
    }

    public function testGetManagers()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();
        $client->expects()->httpPostJson('cgi-bin/service/get_admin_list', [
            'auth_corpid' => 'mock-auth-corp-id',
            'agentid' => 'mock-agent-id',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getManagers('mock-auth-corp-id', 'mock-agent-id'));
    }

    public function testGetOAuthRedirectUrl()
    {
        $app = new ServiceContainer(['suite_id' => 'mock-suit-id']);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app);

        $expected = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=mock-suit-id&redirect_uri=mock-redirect-uri&response_type=code&scope=mock-scope&state=mock-state#wechat_redirect';
        $this->assertSame($expected, $client->getOAuthRedirectUrl('mock-redirect-uri', 'mock-scope', 'mock-state'));
    }

    public function testGetOAuthRedirectUrlWithDefaultParam()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
            'redirect_uri_oauth' => 'mock-redirect-uri',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app);

        $expected = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=mock-suit-id&redirect_uri=mock-redirect-uri&response_type=code&scope=snsapi_userinfo&state=mock-state#wechat_redirect';
        $this->assertSame($expected, $client->getOAuthRedirectUrl('', 'snsapi_userinfo', 'mock-state'));
    }

    public function testGetUserByCode()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();
        $client->expects()->httpGet('cgi-bin/service/getuserinfo3rd', [
            'code' => 'mock-code',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getUserByCode('mock-code'));
    }

    public function testGetUserByTicket()
    {
        $app = new ServiceContainer([
            'suite_id' => 'mock-suit-id',
        ]);
        $app['suite_access_token'] = \Mockery::mock(AccessToken::class);
        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();
        $client->expects()->httpPostJson('cgi-bin/service/getuserdetail3rd', [
            'user_ticket' => 'mock-user-ticket',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getUserByTicket('mock-user-ticket'));
    }
}
