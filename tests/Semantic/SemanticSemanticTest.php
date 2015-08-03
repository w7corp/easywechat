<?php

use EasyWeChat\Semantic\Semantic;
use EasyWeChat\Support\Collection;

class SemanticSemanticTest extends TestCase
{
    /**
     * Test query()
     */
    public function testQuery()
    {
        $http = Mockery::mock('EasyWeChat\Core\Http');
        $http->shouldReceive('json')->andReturnUsing(function($api, $params){
            return $params;
        });
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $semantic = new Semantic('overtrue', $http);

        $expect = new Collection([
                'query'    => 'foo',
                'category' => 'bar',
                'appid'    => 'overtrue',
            ]);

        $this->assertEquals($expect, $semantic->query('foo', 'bar'));
        $this->assertEquals($expect, $semantic->query('foo', ['bar']));

        $expect = new Collection([
                'query'    => 'foo',
                'category' => 'bar,baz',
                'appid'    => 'overtrue',
            ]);

        $this->assertEquals($expect, $semantic->query('foo', ['bar', 'baz']));

        $expect = new Collection([
                'query'    => 'foo',
                'category' => 'bar,baz',
                'appid'    => 'overtrue',
                'foo' => 'bar',
            ]);

        $this->assertEquals($expect, $semantic->query('foo', ['bar', 'baz'], ['foo' => 'bar']));
    }
}