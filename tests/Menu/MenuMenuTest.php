<?php

use EasyWeChat\Menu\Menu;
use EasyWeChat\Support\Collection;

class MenuMenuTest extends TestCase
{
    /**
     * Test set().
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testSet()
    {
        $http = Mockery::mock(EasyWeChat\Core\Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('json')->andReturnUsing(function ($api, $menus) {
            return $menus;
        });

        $menu = new Menu($http);

        $excepted = $menus = [
            [
                'name' => '博客',
                'type' => 'view',
                'key' => 'http://overtrue.me',
            ],
            [
                'name' => '更多',
                'type' => null,
                'key' => null,
                'sub_button' => [
                    [
                        'name' => 'GitHub',
                        'type' => 'view',
                        'key' => 'https://github.com/overtrue',
                    ],
                    [
                        'name' => '微博',
                        'type' => 'view',
                        'key' => 'http://weibo.com/44294631',
                    ],
                ],
            ],
        ];
        // test collection
        $menus[1]['sub_button'] = new Collection($menus[1]['sub_button']);
        $response = $menu->set($menus);

        $this->assertEquals($excepted, $response['button']);

        $response = $menu->set(function () use ($menus) {
            // test collection
            $menus[1]['sub_button'][0] = new Collection($menus[1]['sub_button'][0]);

            return $menus;
        });

        $this->assertEquals($excepted, $response['button']);

        $menu->set('foooooo');//exception
    }

    /**
     * Test get().
     */
    public function testGet()
    {
        $http = Mockery::mock(EasyWeChat\Core\Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturnUsing(function () {
            return ['menu' => ['button' => 'foo']];
        });
        $menu = new Menu($http);

        $response = $menu->get();

        $this->assertEquals('foo', $response);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $http = Mockery::mock(EasyWeChat\Core\Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturnUsing(function () {
            return true;
        });
        $menu = new Menu($http);

        $response = $menu->delete();

        $this->assertTrue($response);
    }

    /**
     * Test current().
     */
    public function testCurrent()
    {
        $http = Mockery::mock(EasyWeChat\Core\Http::class);
        $http->shouldReceive('setExpectedException')->andReturn($http);
        $http->shouldReceive('get')->andReturnUsing(function () {
            return ['foo', 'bar'];
        });
        $menu = new Menu($http);

        $response = $menu->current();

        $this->assertEquals(['foo', 'bar'], $response);
    }
}
