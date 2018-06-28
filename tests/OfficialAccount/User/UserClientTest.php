<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\User;

use EasyWeChat\OfficialAccount\User\UserClient;
use EasyWeChat\Tests\TestCase;

class UserClientTest extends TestCase
{
    public function testGet()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpGet('cgi-bin/user/info', [
            'openid' => 'mock-openid',
            'lang' => 'zh_CN',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->get('mock-openid'));

        $client->expects()->httpGet('cgi-bin/user/info', [
            'openid' => 'mock-openid',
            'lang' => 'en',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->get('mock-openid', 'en'));
    }

    public function testSelect()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpPostJson('cgi-bin/user/info/batchget', [
            'user_list' => [
                [
                    'openid' => 'mock-openid1',
                    'lang' => 'zh_CN',
                ],
                [
                    'openid' => 'mock-openid2',
                    'lang' => 'zh_CN',
                ],
            ],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->select(['mock-openid1', 'mock-openid2']));

        $client->expects()->httpPostJson('cgi-bin/user/info/batchget', [
            'user_list' => [
                [
                'openid' => 'mock-openid1',
                'lang' => 'en',
                ],
                [
                    'openid' => 'mock-openid2',
                    'lang' => 'en',
                ],
            ],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->select(['mock-openid1', 'mock-openid2'], 'en'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpGet('cgi-bin/user/get', ['next_openid' => null])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list());

        $client->expects()->httpGet('cgi-bin/user/get', ['next_openid' => 'mock-openid'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->list('mock-openid'));
    }

    public function testRemark()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpPostJson('cgi-bin/user/info/updateremark', [
            'openid' => 'mock-openid',
            'remark' => 'mock-remark',
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->remark('mock-openid', 'mock-remark'));
    }

    public function testBacklist()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/members/getblacklist', ['begin_openid' => null])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->blacklist());

        $client->expects()->httpPostJson('cgi-bin/tags/members/getblacklist', ['begin_openid' => 'mock-openid'])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->blacklist('mock-openid'));
    }

    public function testBlock()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/members/batchblacklist', [
            'openid_list' => ['mock-openid1'],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->block('mock-openid1'));

        $client->expects()->httpPostJson('cgi-bin/tags/members/batchblacklist', [
            'openid_list' => ['mock-openid1', 'mock-openid2'],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->block(['mock-openid1', 'mock-openid2']));
    }

    public function testUnblock()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/members/batchunblacklist', [
            'openid_list' => ['mock-openid1'],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->unblock('mock-openid1'));

        $client->expects()->httpPostJson('cgi-bin/tags/members/batchunblacklist', [
            'openid_list' => ['mock-openid1', 'mock-openid2'],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->unblock(['mock-openid1', 'mock-openid2']));
    }

    public function testChangeOpenid()
    {
        $client = $this->mockApiClient(UserClient::class);

        $client->expects()->httpPostJson('cgi-bin/changeopenid', [
            'from_appid' => 'old-appid',
            'openid_list' => ['openid1', 'openid2'],
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->changeOpenid('old-appid', ['openid1', 'openid2']));
    }
}
