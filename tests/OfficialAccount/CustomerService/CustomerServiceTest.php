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

use EasyWeChat\OfficialAccount\CustomerService\CustomerService;
use EasyWeChat\Tests\TestCase;

class CustomerServiceTest extends TestCase
{
    public function getCustomerService()
    {
        $customerService = \Mockery::mock('EasyWeChat\OfficialAccount\CustomerService\CustomerService[parseJSON]', [\Mockery::mock('EasyWeChat\OfficialAccount\Core\AccessToken')]);
        $customerService->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
                'quires' => empty($params[3]) ? null : $params[3],
            ];
        });

        return $customerService;
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $customerService = $this->getCustomerService();

        $this->assertStringStartsWith(CustomerService::API_LISTS, $customerService->lists()['api']);
    }

    /**
     * Test onlines().
     */
    public function testOnlines()
    {
        $customerService = $this->getCustomerService();

        $this->assertStringStartsWith(CustomerService::API_ONLINE, $customerService->onlines()['api']);
    }

    /**
     * Test create().
     */
    public function testCreate()
    {
        $customerService = $this->getCustomerService();

        $response = $customerService->create('anzhengchao@test', 'overtrue');

        $this->assertStringStartsWith(CustomerService::API_CREATE, $response['api']);
        $this->assertEquals('anzhengchao@test', $response['params']['kf_account']);
        $this->assertEquals('overtrue', $response['params']['nickname']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $customerService = $this->getCustomerService();

        $response = $customerService->update('anzhengchao@test', 'overtrue');

        $this->assertStringStartsWith(CustomerService::API_UPDATE, $response['api']);
        $this->assertEquals('anzhengchao@test', $response['params']['kf_account']);
        $this->assertEquals('overtrue', $response['params']['nickname']);
    }

    /**
     * Test invite().
     */
    public function testInvite()
    {
        $customerService = $this->getCustomerService();

        $response = $customerService->invite('anzhengchao@test', 'overtrue');

        $this->assertStringStartsWith(CustomerService::API_INVITE_BIND, $response['api']);
        $this->assertEquals('anzhengchao@test', $response['params']['kf_account']);
        $this->assertEquals('overtrue', $response['params']['invite_wx']);
    }

    /**
     * Test delete().
     */
    //public function testDelete()
    //{
        // 这里 不 TM 测了
        // $customerService = $this->getCustomerService();

        // $response = $customerService->delete('anzhengchao@test');

        // $this->assertStringStartsWith(CustomerService::API_DELETE, $response['api']);
        // $this->assertContains('kf_account=anzhengchao@test', $response['api']);
    //}

    /**
     * Test avatar().
     */
    public function testAvatar()
    {
        $customerService = $this->getCustomerService();

        $response = $customerService->avatar('anzhengchao@test', '/foobar/avatar.jpg');

        $this->assertStringStartsWith(CustomerService::API_AVATAR_UPLOAD, $response['api']);
        $this->assertContains('anzhengchao@test', $response['quires']['kf_account']);
        $this->assertEquals(['media' => '/foobar/avatar.jpg'], $response['params']);
    }
}
