<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\Kf;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\Kf\AccountClient;

/**
 * Class ClientTest.
 *
 * @package EasyWeChat\Tests\Work\Live
 *
 * @author 读心印 <aa24615@qq.com>
 */
class AccountClientTest extends TestCase
{
    public function testAdd()
    {
        $client = $this->mockApiClient(AccountClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/account/add', [
            'name' => '新建的客服帐号',
            'media_id' => 'media_id'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->add('新建的客服帐号', 'media_id'));
    }

    public function testDel()
    {
        $client = $this->mockApiClient(AccountClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/account/del', [
            'open_kfid' => 'open_xxxxx'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->del('open_xxxxx'));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(AccountClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/account/update', [
            'open_kfid' => 'wkAJ2GCAAAZSfhHCt7IFSvLKtMPxyJTw',
            'name' => '修改客服名',
            'media_id' => 'media_xxxx'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update('wkAJ2GCAAAZSfhHCt7IFSvLKtMPxyJTw', '修改客服名', 'media_xxxx'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(AccountClient::class);
        $client->expects()->httpGet('cgi-bin/kf/account/list')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list());
    }

    public function testGetAccountLink()
    {
        $client = $this->mockApiClient(AccountClient::class);
        $client->expects()->httpPostJson('cgi-bin/kf/add_contact_way', [
            'open_kfid' => 'OPEN_KFID',
            'scene' => '12345'
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAccountLink('OPEN_KFID', '12345'));
    }
}
