<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use EasyWeChat\Menu\Menu;

class MenuMenuTest extends TestCase
{
    public function getMenu()
    {
        $menu = Mockery::mock('EasyWeChat\Menu\Menu[parseJSON]', [Mockery::mock('EasyWeChat\Core\AccessToken')]);
        $menu->shouldReceive('parseJSON')->andReturnUsing(function ($method, $params) {
            return [
                'api' => $params[0],
                'params' => empty($params[1]) ? null : $params[1],
            ];
        });

        return $menu;
    }

    /**
     * Test add().
     */
    public function testAdd()
    {
        $menu = $this->getMenu();

        $excepted = $buttons = [
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

        // normal
        $response = $menu->add($buttons);
        $this->assertStringStartsWith(Menu::API_CREATE, $response['api']);
        $this->assertEquals($excepted, $response['params']['button']);

        // conditional
        $matchRule = ['group_id' => 2, 'province' => '广东'];
        $response = $menu->add($buttons, $matchRule);
        $this->assertStringStartsWith(Menu::API_CONDITIONAL_CREATE, $response['api']);
        $this->assertEquals($excepted, $response['params']['button']);
        $this->assertEquals($matchRule, $response['params']['matchrule']);
    }

    /**
     * Test all().
     */
    public function testAll()
    {
        $menu = $this->getMenu();

        $response = $menu->all();
        $this->assertStringStartsWith(Menu::API_GET, $response['api']);
    }

    /**
     * Test current().
     */
    public function testCurrent()
    {
        $menu = $this->getMenu();

        $response = $menu->current();
        $this->assertStringStartsWith(Menu::API_QUERY, $response['api']);
    }

    /**
     * Test destroy().
     */
    public function testDestroy()
    {
        $menu = $this->getMenu();

        $response = $menu->destroy();
        $this->assertStringStartsWith(Menu::API_DELETE, $response['api']);

        $response = $menu->destroy(23);
        $this->assertStringStartsWith(Menu::API_CONDITIONAL_DELETE, $response['api']);
        $this->assertEquals(23, $response['params']['menuid']);
    }

    /**
     * Test test().
     */
    public function testTest()
    {
        $menu = $this->getMenu();

        $response = $menu->test(234);

        $this->assertStringStartsWith(Menu::API_CONDITIONAL_TEST, $response['api']);
        $this->assertEquals(234, $response['params']['user_id']);
    }
}
