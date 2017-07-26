<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\Applications\WeWork\Department;

use EasyWeChat\Tests\TestCase;
use Mockery as m;

class ClientTest extends TestCase
{
    /**
     * Test create().
     */
    public function testCreate()
    {
        $data = [
            'name' => '广州研发中心',
            'parentid' => 1,
            'order' => 1,
            'id' => 2,
        ];
        $result = $this->getClient()->create($data);

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/department/create', $result[1][0]);
        $this->assertSame($data, $result[1][1]);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $data = [
            'name' => '广州研发中心',
            'parentid' => 1,
            'order' => 1,
            'id' => 2,
        ];
        $result = $this->getClient()->update($data);

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/department/update', $result[1][0]);
        $this->assertSame($data, $result[1][1]);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $result = $this->getClient()->delete(1);

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/department/delete', $result[1][0]);
        $this->assertSame(1, $result[1][1]['id']);
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $result = $this->getClient()->lists();

        $this->assertSame('https://qyapi.weixin.qq.com/cgi-bin/department/list', $result[1][0]);
        $this->assertNull($result[1][1]['id']);

        $result = $this->getClient()->lists(1);

        $this->assertSame(1, $result[1][1]['id']);
    }

    /**
     * Get Department Client.
     *
     * @return \EasyWeChat\Applications\WeWork\Department\Client
     */
    private function getClient()
    {
        return m::mock('EasyWeChat\Applications\WeWork\Department\Client[parseJSON]', [m::mock('EasyWeChat\Applications\WeWork\Core\AccessToken')], function ($mock) {
            $mock->shouldReceive('parseJSON')->andReturnUsing(function (...$args) {
                return $args;
            });
        });
    }
}
