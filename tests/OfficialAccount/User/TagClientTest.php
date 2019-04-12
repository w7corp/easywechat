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

use EasyWeChat\OfficialAccount\User\TagClient;
use EasyWeChat\Tests\TestCase;

class TagClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/create', [
            'tag' => ['name' => '粉丝'],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->create('粉丝'));
    }

    public function testList()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpGet('cgi-bin/tags/get')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->list());
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/update', [
            'tag' => [
                'id' => 12,
                'name' => '粉丝',
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(12, '粉丝'));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/delete', [
            'tag' => [
                'id' => 12,
            ],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete(12));
    }

    public function testUserTags()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/getidlist', [
            'openid' => 'mock-openid',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userTags('mock-openid'));
    }

    public function testUsersOfTag()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/user/tag/get', [
            'tagid' => 45,
            'next_openid' => '',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->usersOfTag(45));

        $client->expects()->httpPostJson('cgi-bin/user/tag/get', [
            'tagid' => 45,
            'next_openid' => 'mock-openid',
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->usersOfTag(45, 'mock-openid'));
    }

    public function testTagUsers()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/members/batchtagging', [
            'openid_list' => ['openid1', 'openid2'],
            'tagid' => 45,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->tagUsers(['openid1', 'openid2'], 45));
    }

    public function testUntagUsers()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tags/members/batchuntagging', [
            'openid_list' => ['openid1', 'openid2'],
            'tagid' => 45,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->untagUsers(['openid1', 'openid2'], 45));
    }
}
