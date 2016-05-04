<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Core\AccessToken;
use EasyWeChat\Semantic\Semantic;

class SemanticSemanticTest extends TestCase
{
    /**
     * Test query().
     */
    public function testQuery()
    {
        $semantic = Mockery::mock('EasyWeChat\Semantic\Semantic[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
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
