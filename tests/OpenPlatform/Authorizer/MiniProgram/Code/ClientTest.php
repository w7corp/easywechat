<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OpenPlatform\Authorizer\MiniProgram\Code;

use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Code\Client;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testCommit()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));

        $client->expects()->httpPostJson('wxa/commit', [
            'template_id' => 123,
            'ext_json' => '{"foo":"bar"}',
            'user_version' => 'v1.0',
            'user_desc' => 'First commit.',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->commit(123, '{"foo":"bar"}', 'v1.0', 'First commit.'));
    }

    public function testGetQrCode()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->requestRaw('wxa/get_qrcode', 'GET', ['query' => ['path' => '']])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getQrCode());
    }

    public function testGetQrCodeWithParamPath()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->requestRaw('wxa/get_qrcode', 'GET', ['query' => ['path' => 'page/index?action=1']])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getQrCode('page/index?action=1'));
    }

    public function testGetCategory()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpGet('wxa/get_category')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getCategory());
    }

    public function testGetPage()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpGet('wxa/get_page')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getPage());
    }

    public function testSubmitAudit()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/submit_audit', ['item_list' => ['foo', 'bar']])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->submitAudit(['foo', 'bar']));
    }

    public function testGetAuditStatus()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/get_auditstatus', ['auditid' => 123])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getAuditStatus(123));
    }

    public function testGetLatestAuditStatus()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpGet('wxa/get_latest_auditstatus')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getLatestAuditStatus());
    }

    public function testRelease()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/release')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->release());
    }

    public function testChangeVisitStatus()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/change_visitstatus', ['action' => 'foo'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->changeVisitStatus('foo'));
    }

    public function testGrayRelease()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpPostJson('wxa/grayrelease', ['gray_percentage' => 20])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->grayRelease(20));
    }

    public function testRevertGrayRelease()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpGet('wxa/revertgrayrelease')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->revertGrayRelease());
    }

    public function testGetGrayRelease()
    {
        $client = $this->mockApiClient(Client::class, [], new ServiceContainer(['app_id' => 'app-id']));
        $client->expects()->httpGet('wxa/getgrayreleaseplan')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getGrayRelease());
    }
}
