<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Tests\OfficialAccount\Semantic;

use EasyWeChat\OfficialAccount\Core\AccessToken;
use EasyWeChat\OfficialAccount\Semantic\Client as Semantic;
use EasyWeChat\Tests\TestCase;

class ClientTest extends TestCase
{
    /**
     * Test query().
     */
    public function testQuery()
    {
        $semantic = \Mockery::mock('EasyWeChat\OfficialAccount\Semantic\Client[parseJSON]', [\Mockery::mock('EasyWeChat\OfficialAccount\Core\AccessToken')]);
        $semantic->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });
        $accessToken = new AccessToken('overtrue', 'bar');
        $semantic->setAccessToken($accessToken);

        $expect = [
                'query' => 'foo',
                'category' => 'bar',
                'appid' => 'overtrue',
            ];

        $this->assertStringStartsWith(Semantic::API_SEARCH, $semantic->query('foo', 'bar')['api']);
        $this->assertEquals($expect, $semantic->query('foo', 'bar')['params']);
        $this->assertEquals($expect, $semantic->query('foo', ['bar'])['params']);

        $expect = [
                'query' => 'foo',
                'category' => 'bar,baz',
                'appid' => 'overtrue',
            ];

        $this->assertEquals($expect, $semantic->query('foo', ['bar', 'baz'])['params']);

        $expect = [
                'query' => 'foo',
                'category' => 'bar,baz',
                'appid' => 'overtrue',
                'foo' => 'bar',
            ];

        $this->assertEquals($expect, $semantic->query('foo', ['bar', 'baz'], ['foo' => 'bar'])['params']);
    }
}
