<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\POI\POI;

class POIPOITest extends TestCase
{
    public function getPOI()
    {
        $POI = Mockery::mock('EasyWeChat\POI\POI[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $POI->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $POI;
    }

    /**
     * Test get().
     */
    public function testGet()
    {
        $POI = $this->getPOI();

        $response = $POI->get('foo');
        $this->assertStringStartsWith(POI::API_GET, $response['api']);
        $this->assertEquals('foo', $response['params']['poi_id']);
    }

    /**
     * Test lists().
     */
    public function testLists()
    {
        $POI = $this->getPOI();

        $response = $POI->lists();
        $this->assertStringStartsWith(POI::API_LIST, $response['api']);
        $this->assertArrayHasKey('begin', $response['params']);
        $this->assertArrayHasKey('limit', $response['params']);

        $response = $POI->lists(10, 30);
        $this->assertEquals(10, $response['params']['begin']);
        $this->assertEquals(30, $response['params']['limit']);
    }

    /**
     * Test create().
     */
    public function testCreate()
    {
        $POI = $this->getPOI();

        $data = ['foo' => 'bar'];

        $response = $POI->create($data);
        $this->assertStringStartsWith(POI::API_CREATE, $response['api']);
        $this->assertEquals(['business' => ['base_info' => $data]], $response['params']);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $POI = $this->getPOI();

        $response = $POI->update('foo', ['foo' => 'bar']);
        $this->assertStringStartsWith(POI::API_UPDATE, $response['api']);
        $this->assertEquals(['business' => ['base_info' => ['poi_id' => 'foo', 'foo' => 'bar']]], $response['params']);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $POI = $this->getPOI();

        $response = $POI->delete('foo');
        $this->assertStringStartsWith(POI::API_DELETE, $response['api']);
        $this->assertEquals('foo', $response['params']['poi_id']);
    }
}
