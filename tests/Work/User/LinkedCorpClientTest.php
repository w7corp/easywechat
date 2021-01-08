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
use EasyWeChat\Work\User\LinkedCorpClient;

class LinkedCorpClientTest extends TestCase
{
    public function testGetAgentPermissions()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/agent/get_perm_list')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAgentPermissions());
    }

    public function testGetUser()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'userid' => 'CORPID/USERID'
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/user/get', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUser('CORPID/USERID'));
    }

    public function testGetUsers()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'department_id' => 'LINKEDID/DEPARTMENTID',
            'fetch_child' => 1
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/user/simplelist', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getUsers('LINKEDID/DEPARTMENTID', true));
    }

    public function testGetDetailedUsers()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'department_id' => 'LINKEDID/DEPARTMENTID',
            'fetch_child' => 1
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/user/list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getDetailedUsers('LINKEDID/DEPARTMENTID', true));
    }

    public function testGetDepartments()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'department_id' => 'LINKEDID/DEPARTMENTID'
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/department/list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getDepartments('LINKEDID/DEPARTMENTID'));
    }
}
