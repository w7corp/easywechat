<?php

use EasyWeChat\Menu\Item;

class MenuItemTest extends TestCase
{
    /**
     * Test __construct()
     */
    public function testBuild()
    {
        $item = new Item('foo');
        $this->assertEquals('foo', $item['name']);

        $item = new Item('foo', 'view', 'http://easywechat.org');
        $this->assertEquals('view', $item['type']);
        $this->assertEquals('http://easywechat.org', $item['url']);

        $item = new Item('foo', 'media_id', 'KQb_w_Tiz-nSdVLoTV35Psmty8hGBulGhEdbb9SKs-o');
        $this->assertArrayHasKey('media_id', $item);
        $this->assertEquals('KQb_w_Tiz-nSdVLoTV35Psmty8hGBulGhEdbb9SKs-o', $item['media_id']);

        $item = new Item('foo', 'view_limited', 'KQb_w_Tiz-nSdVLoTV35Psmty8hGBulGhEdbb9SKs-o');
        $this->assertArrayHasKey('media_id', $item);
        $this->assertEquals('KQb_w_Tiz-nSdVLoTV35Psmty8hGBulGhEdbb9SKs-o', $item['media_id']);

        $item = new Item('foo', 'bar', 'TEST_KEY_VALUE');
        $this->assertArrayHasKey('key', $item);
        $this->assertEquals('TEST_KEY_VALUE', $item['key']);
    }

    /**
     * Test buttons()
     */
    public function testButtons()
    {
        $item = new Item('foo');

        $buttons = [
            new Item('foo', 'click', 'foooooo'),
            ['bar', 'view', 'http://easywechat.org'],
        ];
        $item->buttons($buttons);
        $excepted = [
            [
                'name' => 'foo',
                'type' => 'click',
                'key' => 'foooooo',
            ],
            [
                'name' => 'bar',
                'type' => 'view',
                'url' => 'http://easywechat.org'
            ],
        ];
        $this->assertEquals($excepted, $item['sub_button']);

        $item = new Item('overtrue');
        $item->buttons(function() use($buttons){
            return $buttons;
        });
        $this->assertEquals($excepted, $item['sub_button']);

        $item = new Item('overtrue');
        $item->button(new Item('foo', 'view', 'http://overtrue.me'));
        $excepted = [
            'name' => 'foo',
            'type' => 'view',
            'url' => 'http://overtrue.me',
        ];

        $this->assertEquals($excepted, $item['sub_button'][0]);
    }

    /**
     * Test button() with error data.
     *
     * @expectedException EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function testButtonException()
    {
        $item = new Item('foo', 'view', 'http://easywechat.org');
        $item->button('foo');
    }
}