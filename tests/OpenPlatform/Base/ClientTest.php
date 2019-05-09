<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Base;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Base\Client;
use EasyWeChat\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ClientTest extends TestCase
{
    public function testHandleAuthorizeWithAuthCode()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('cgi-bin/component/api_query_auth', [
            'component_appid' => '123456',
            'authorization_code' => 'auth-code',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->handleAuthorize('auth-code'));
    }

    public function testHandleAuthorizeWithoutAuthCode()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456'], ['request' => new Request(['auth_code' => 'auth-code-from-request'])]));

        $client->expects()->httpPostJson('cgi-bin/component/api_query_auth', [
            'component_appid' => '123456',
            'authorization_code' => 'auth-code-from-request',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->handleAuthorize());
    }

    public function testGetAuthorizer()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('cgi-bin/component/api_get_authorizer_info', [
            'component_appid' => '123456',
            'authorizer_appid' => '654321',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAuthorizer('654321'));
    }

    public function testGetAuthorizerOption()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('cgi-bin/component/api_get_authorizer_option', [
            'component_appid' => '123456',
            'authorizer_appid' => '654321',
            'option_name' => 'foobar',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAuthorizerOption('654321', 'foobar'));
    }

    public function testSetAuthorizerOption()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('cgi-bin/component/api_set_authorizer_option', [
            'component_appid' => '123456',
            'authorizer_appid' => '654321',
            'option_name' => 'foobar',
            'option_value' => 'baz',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->setAuthorizerOption('654321', 'foobar', 'baz'));
    }

    public function testGetAuthorizers()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()->httpPostJson('cgi-bin/component/api_get_authorizer_list', [
            'component_appid' => '123456',
            'offset' => '0',
            'count' => '500',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAuthorizers());

        $client->expects()->httpPostJson('cgi-bin/component/api_get_authorizer_list', [
            'component_appid' => '123456',
            'offset' => '20',
            'count' => '100',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAuthorizers(20, 100));
    }

    public function testCreatePreAuthorizationCode()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()
            ->httpPostJson('cgi-bin/component/api_create_preauthcode', ['component_appid' => '123456'])
            ->andReturn('mock-result')
            ;

        $result = $client->createPreAuthorizationCode();

        $this->assertSame('mock-result', $result);
    }

    /**
     * @uses \EasyWeChat\OpenPlatform\Base\Client::clearQuota()
     */
    public function testClearQuota()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => '123456']));

        $client->expects()
            ->httpPostJson('cgi-bin/component/clear_quota', ['component_appid' => '123456'])
            ->andReturn('mock-result')
            ;

        $result = $client->clearQuota();

        $this->assertSame('mock-result', $result);
    }
}
