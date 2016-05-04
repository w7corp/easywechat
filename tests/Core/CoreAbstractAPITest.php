<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Http;
use EasyWeChat\Support\Collection;

class FooAPI extends AbstractAPI
{
    public function getHttpInstance()
    {
        return $this->http;
    }
}

class CoreAbstractAPITest extends TestCase
{
    /**
     * Test __construct.
     */
    public function testConstruct()
    {
        $accessToken = Mockery::mock(AccessToken::class);

        $api = new FooAPI($accessToken);
        $this->assertEquals($accessToken, $api->getAccessToken());
    }

    public function testHttpInstance()
    {
        $accessToken = Mockery::mock(AccessToken::class);

        $api = new FooAPI($accessToken);

        $this->assertNull($api->getHttpInstance());

        $api->getHttp();
        $this->assertInstanceOf(Http::class, $api->getHttpInstance());

        $middlewares = $api->getHttp()->getMiddlewares();
        $this->assertCount(3, $middlewares);

        $http = Mockery::mock(Http::class.'[getMiddlewares]', function ($mock) {
            $mock->shouldReceive('getMiddlewares')->andReturn([1, 2, 3]);
        });
        $api->setHttp($http);
        $this->assertEquals($http, $api->getHttp());
    }

    public function testParseJSON()
    {
        $accessToken = Mockery::mock(AccessToken::class);

        $api = new FooAPI($accessToken);
        $http = Mockery::mock(Http::class.'[getMiddlewares,get,parseJSON]', function ($mock) {
            $mock->shouldReceive('getMiddlewares')->andReturn([1, 2, 3]);
            $mock->shouldReceive('get')->andReturnUsing(function () {
                return func_get_args();
            });
            $mock->shouldReceive('parseJSON')->andReturnUsing(function ($json) {
                return $json;
            });
        });
        $api->setHttp($http);

        $collection = $api->parseJSON('get', ['foo', ['bar']]);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(['foo', ['bar']], $collection->all());

        // test error
        $http = Mockery::mock(Http::class.'[getMiddlewares,get,parseJSON]', function ($mock) {
            $mock->shouldReceive('getMiddlewares')->andReturn([1, 2, 3]);
            $mock->shouldReceive('get')->andReturnUsing(function () {
                return func_get_args();
            });
            $mock->shouldReceive('parseJSON')->andReturnUsing(function ($json) {
                return ['errcode' => 24000];
            });
        });
        $api->setHttp($http);

        $this->setExpectedException(\EasyWeChat\Core\Exceptions\HttpException::class, 'Unknown', 24000);
        $collection = $api->parseJSON('get', ['foo', ['bar']]);
        $this->fail();
    }
}
