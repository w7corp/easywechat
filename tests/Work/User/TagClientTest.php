<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Work\User;

use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\User\TagClient;

class TagClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tag/create', ['tagname' => '粉丝', 'tagid' => null])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create('粉丝'));

        // with id
        $client->expects()->httpPostJson('cgi-bin/tag/create', ['tagname' => '粉丝', 'tagid' => 1])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create('粉丝', 1));
    }

    public function testList()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpGet('cgi-bin/tag/list')->andReturn('mock-result');
        $this->assertSame('mock-result', $client->list());
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tag/update', [
            'tagid' => 12,
            'tagname' => '粉丝',
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->update(12, '粉丝'));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpGet('cgi-bin/tag/delete', [
            'tagid' => 12,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->delete(12));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpGet('cgi-bin/tag/get', [
            'tagid' => 12,
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get(12));
    }

    public function testTagUsers()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tag/addtagusers', [
            'tagid' => 12,
            'userlist' => ['foo', 'bar'],
            'partylist' => [],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->tagUsers(12, ['foo', 'bar']));
    }

    public function testUntagUsers()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tag/deltagusers', [
            'tagid' => 12,
            'userlist' => ['foo', 'bar'],
            'partylist' => [],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->untagUsers(12, ['foo', 'bar']));
    }

    public function testTagDepartments()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tag/addtagusers', [
            'tagid' => 12,
            'userlist' => [],
            'partylist' => [14, 26],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->tagDepartments(12, [14, 26]));
    }

    public function testUntagDepartments()
    {
        $client = $this->mockApiClient(TagClient::class);

        $client->expects()->httpPostJson('cgi-bin/tag/deltagusers', [
            'tagid' => 12,
            'userlist' => [],
            'partylist' => [14, 26],
        ])->andReturn('mock-result');

        $this->assertSame('mock-result', $client->untagDepartments(12, [14, 26]));
    }
}
