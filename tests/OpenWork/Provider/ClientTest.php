<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenWork\Provider;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenWork\Auth\AccessToken;
use EasyWeChat\OpenWork\Provider\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetLoginUrl()
    {
        $app = new ServiceContainer(['corp_id' => 'mock-corp-id']);
        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app);

        $expected = 'https://open.work.weixin.qq.com/wwopen/sso/3rd_qrConnect?appid=mock-corp-id&redirect_uri=mock-redirect-uri&usertype=mock-user-type&state=mock-state';
        $this->assertSame($expected, $client->getLoginUrl('mock-redirect-uri', 'mock-user-type', 'mock-state'));
    }

    public function testGetOAuthRedirectUrlWithDefaultParam()
    {
        $app = new ServiceContainer([
            'corp_id' => 'mock-corp-id',
            'redirect_uri_single' => 'mock-redirect-uri',
        ]);
        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app);

        $expected = 'https://open.work.weixin.qq.com/wwopen/sso/3rd_qrConnect?appid=mock-corp-id&redirect_uri=mock-redirect-uri&usertype=admin&state=mock-state';

        $this->assertSame($expected, $client->getLoginUrl('', 'admin', 'mock-state'));
    }

    public function testGetLoginInfo()
    {
        $app = new ServiceContainer(['corp_id' => 'mock-corp-id']);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();

        $client->expects()->httpPostJson('cgi-bin/service/get_login_info', [
            'auth_code' => 'mock-auth-code',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getLoginInfo('mock-auth-code'));
    }

    public function testGetRegisterUri()
    {
        $app = new ServiceContainer(['corp_id' => 'mock-corp-id']);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();

        $this->assertSame('https://open.work.weixin.qq.com/3rdservice/wework/register?register_code=mock-register-code', $client->getRegisterUri('mock-register-code'));
    }

    public function testGetRegisterUriWithDefaultParam()
    {
        $app = new ServiceContainer(['corp_id' => 'mock-corp-id']);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, ['getRegisterCode'], $app)->makePartial();

        $client->expects()->getRegisterCode()->andReturn([
            'register_code' => 'mock-register-code',
        ]);

        $this->assertSame('https://open.work.weixin.qq.com/3rdservice/wework/register?register_code=mock-register-code', $client->getRegisterUri());
    }

    public function testGetRegisterCode()
    {
        $app = new ServiceContainer([
            'corp_id' => 'mock-corp-id',
            'reg_template_id' => 'mock-reg-template-id',
        ]);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();

        $client->expects()->httpPostJson('cgi-bin/service/get_register_code', [
            'template_id' => 'mock-reg-template-id',
            'corp_name' => 'mock-corp-name',
            'admin_name' => 'mock-admin-name',
            'admin_mobile' => 'mock-admin-mobile',
            'state' => 'mock-state',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getRegisterCode('mock-corp-name', 'mock-admin-name', 'mock-admin-mobile', 'mock-state'));
    }

    public function testGetRegisterCodeWithDefaultParam()
    {
        $app = new ServiceContainer([
            'corp_id' => 'mock-corp-id',
            'reg_template_id' => 'mock-reg-template-id',
        ]);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();

        $client->expects()->httpPostJson('cgi-bin/service/get_register_code', [
            'template_id' => 'mock-reg-template-id',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getRegisterCode());
    }

    public function testGetRegisterInfo()
    {
        $app = new ServiceContainer(['corp_id' => 'mock-corp-id']);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();

        $client->expects()->httpPostJson('cgi-bin/service/get_register_info', [
            'register_code' => 'mock-register-code',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getRegisterInfo('mock-register-code'));
    }

    public function testSetAgentScope()
    {
        $app = new ServiceContainer(['corp_id' => 'mock-corp-id']);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();

        $client->expects()->httpGet('cgi-bin/agent/set_scope', [
            'agentid' => 'mock-agent-id',
            'allow_user' => ['user1', 'user2'],
            'allow_party' => ['party1', 'party2'],
            'allow_tag' => ['tag1', 'tag2'],
            'access_token' => 'mock-access-token',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->setAgentScope('mock-access-token', 'mock-agent-id', ['user1', 'user2'], ['party1', 'party2'], ['tag1', 'tag2']));
    }

    public function testContactSyncSuccess()
    {
        $app = new ServiceContainer(['corp_id' => 'mock-corp-id']);

        $app['provider_access_token'] = \Mockery::mock(AccessToken::class);

        $client = $this->mockApiClient(Client::class, [], $app)->makePartial();

        $client->expects()->httpGet('cgi-bin/sync/contact_sync_success', [
            'access_token' => 'mock-access-token',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->contactSyncSuccess('mock-access-token'));
    }
}
