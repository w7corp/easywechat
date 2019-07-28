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

use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Tests\TestCase;
use EasyWeChat\Work\User\Client;

class ClientTest extends TestCase
{
    public function testCreate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/user/create', ['foo' => 'bar'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->create(['foo' => 'bar']));
    }

    public function testUpdate()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/user/update', ['userid' => 'overtrue', 'foo' => 'bar'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->update('overtrue', ['foo' => 'bar']));
    }

    public function testGet()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/user/get', ['userid' => 'overtrue'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->get('overtrue'));
    }

    public function testDelete()
    {
        $client = $this->mockApiClient(Client::class, 'batchDelete');
        $client->expects()->httpGet('cgi-bin/user/delete', ['userid' => 'overtrue'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete('overtrue'));

        $client->expects()->batchDelete(['overtrue', 'foo'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->delete(['overtrue', 'foo']));
    }

    public function testBatchDelete()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/user/batchdelete', ['useridlist' => ['overtrue', 'foo']])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->batchDelete(['overtrue', 'foo']));
    }

    public function testGetDepartmentUsers()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/user/simplelist', [
            'department_id' => 14,
            'fetch_child' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getDepartmentUsers(14));

        $client->expects()->httpGet('cgi-bin/user/simplelist', [
            'department_id' => 15,
            'fetch_child' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getDepartmentUsers(15, true));
    }

    public function testGetDetailedDepartmentUsers()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/user/list', [
            'department_id' => 18,
            'fetch_child' => 0,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getDetailedDepartmentUsers(18));

        $client->expects()->httpGet('cgi-bin/user/list', [
            'department_id' => 18,
            'fetch_child' => 1,
        ])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getDetailedDepartmentUsers(18, true));
    }

    public function testUserIdToOpenid()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/user/convert_to_openid', ['userid' => 'overtrue', 'agentid' => null])
                        ->andReturn('mock-result');
        $this->assertSame('mock-result', $client->userIdToOpenid('overtrue'));

        $client->expects()->httpPostJson('cgi-bin/user/convert_to_openid', ['userid' => 'overtrue', 'agentid' => 39202])
            ->andReturn('mock-result');
        $this->assertSame('mock-result', $client->userIdToOpenid('overtrue', 39202));
    }

    public function testOpenidToUserId()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/user/convert_to_userid', ['openid' => 'mock-openid'])
            ->andReturn('mock-result');
        $this->assertSame('mock-result', $client->openidToUserId('mock-openid'));
    }

    public function testMobileToUserId()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpPostJson('cgi-bin/user/getuserid', ['mobile' => 'mock-mobile'])
            ->andReturn('mock-result');
        $this->assertSame('mock-result', $client->mobileToUserId('mock-mobile'));
    }

    public function testAccept()
    {
        $client = $this->mockApiClient(Client::class);
        $client->expects()->httpGet('cgi-bin/user/authsucc', ['userid' => 'overtrue'])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->accept('overtrue'));
    }

    public function testInvite()
    {
        $client = $this->mockApiClient(Client::class);
        $params = ['user' => ['mock-user-id']];
        $client->expects()->httpPostJson('cgi-bin/batch/invite', $params)->andReturn('mock-result');
        $this->assertSame('mock-result', $client->invite($params));
    }

    public function testGetInvitationQrCode()
    {
        $client = $this->mockApiClient(Client::class);

        try {
            $client->getInvitationQrCode(5);
        } catch (\Exception $e) {
            $this->assertInstanceOf(InvalidArgumentException::class, $e);
            $this->assertSame('The sizeType must be 1, 2, 3, 4.', $e->getMessage());
        }

        $client->expects()->httpGet('cgi-bin/corp/get_join_qrcode', ['size_type' => 1])->andReturn('mock-result');
        $this->assertSame('mock-result', $client->getInvitationQrCode(1));
    }
}
