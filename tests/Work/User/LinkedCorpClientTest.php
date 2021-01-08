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
    public function testGetAgentPerms()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/agent/get_perm_list')->andReturn('mock-result');

        $this->assertSame('mock-result', $client->getAgentPerms());
    }

    public function testGet()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'userid' => 'CORPID/USERID'
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/user/get', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->get('CORPID/USERID'));
    }

    public function testUserSimples()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'department_id' => 'LINKEDID/DEPARTMENTID',
            'fetch_child' => 1
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/user/simplelist', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->userSimples('LINKEDID/DEPARTMENTID', true));
    }

    public function testUsers()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'department_id' => 'LINKEDID/DEPARTMENTID',
            'fetch_child' => 1
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/user/list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->users('LINKEDID/DEPARTMENTID', true));
    }

    public function testDepartments()
    {
        $client = $this->mockApiClient(LinkedCorpClient::class);

        $params = [
            'department_id' => 'LINKEDID/DEPARTMENTID'
        ];

        $client->expects()->httpPostJson('cgi-bin/linkedcorp/department/list', $params)->andReturn('mock-result');

        $this->assertSame('mock-result', $client->departments('LINKEDID/DEPARTMENTID'));
    }
}
