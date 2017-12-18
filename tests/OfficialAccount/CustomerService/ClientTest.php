<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\CustomerService;

use EasyWeChat\OfficialAccount\CustomerService\Client;
use EasyWeChat\OfficialAccount\CustomerService\Messenger;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testList()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/customservice/getkflist')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->list());
    }

    public function testOnline()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpGet('cgi-bin/customservice/getonlinekflist')->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->online());
    }

    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('customservice/kfaccount/add', [
            'kf_account' => 'overtrue@test',
            'nickname' => '小超',
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->create('overtrue@test', '小超'));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('customservice/kfaccount/update', [
            'kf_account' => 'overtrue@test',
            'nickname' => '小小超',
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->update('overtrue@test', '小小超'));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('customservice/kfaccount/del', [], ['kf_account' => 'overtrue@test'])
            ->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->delete('overtrue@test'));
    }

    public function testInvite()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('customservice/kfaccount/inviteworker', [
            'kf_account' => 'overtrue@test',
            'invite_wx' => 'notovertrue',
        ])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->invite('overtrue@test', 'notovertrue'));
    }

    public function testSetAvatar()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpUpload(
            'customservice/kfaccount/uploadheadimg',
            ['media' => '/path/to/image.jpg'],
            [],
            ['kf_account' => 'overtrue@test']
        )->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->setAvatar('overtrue@test', '/path/to/image.jpg'));
    }

    public function testMessage()
    {
        $client = $this->mockApiClient(Client::class);

        $this->assertInstanceOf(Messenger::class, $client->message('text content'));
    }

    public function testSend()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('cgi-bin/message/custom/send', ['foo' => 'bar'])->andReturn('mock-result')->once();

        $this->assertSame('mock-result', $client->send(['foo' => 'bar']));
    }

    public function testMessages()
    {
        $client = $this->mockApiClient(Client::class);

        $client->expects()->httpPostJson('customservice/msgrecord/getmsglist', [
            'starttime' => 1464710400,
            'endtime' => 1464796800,
            'msgid' => 1,
            'number' => 10000,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->messages(1464710400, 1464796800));

        $client->expects()->httpPostJson('customservice/msgrecord/getmsglist', [
            'starttime' => 1464710400,
            'endtime' => 1464796800,
            'msgid' => 2,
            'number' => 100,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->messages(1464710400, 1464796800, 2, 100));

        // string time
        $client->expects()->httpPostJson('customservice/msgrecord/getmsglist', [
            'starttime' => strtotime('2017-08-05 12:00:00'),
            'endtime' => strtotime('2017-08-05 12:01:00'),
            'msgid' => 2,
            'number' => 100,
        ])->andReturn('mock-result')->once();
        $this->assertSame('mock-result', $client->messages('2017-08-05 12:00:00', '2017-08-05 12:01:00', 2, 100));
    }
}
