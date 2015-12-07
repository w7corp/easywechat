<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午5:10
 */
namespace Overtrue\Wechat\Test\Shop;


use Overtrue\Wechat\Image;
use Overtrue\Wechat\Shop\Data\Shelf as ShelfData;
use Overtrue\Wechat\Shop\Shelf;
use Overtrue\Wechat\Test\TestBase;

class ShelfTest extends TestBase
{

    protected $url;

    protected function setUp()
    {
        parent::setUp();

        $img = new Image($this->config->appId,$this->config->appSecret);

        $this->url = $img->upload(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Image'.DIRECTORY_SEPARATOR.'aa.jpg');

    }

    public static function shelf($arry = false)
    {
        $groupId = '207133170';

        $shelf = new ShelfData();
        $shelf->controlTwo(array($groupId,$groupId,$groupId));

        if ($arry) return $shelf->toArray();

        return $shelf;
    }

    public function testAdd()
    {

        $shelfData = $this->shelf();

        $shelf = new Shelf($this->http);
        $response = $shelf->add(function(ShelfData $shelf) use ($shelfData) {
            return $shelfData;
        },$this->url,'test');
        $this->assertTrue(is_array($response));

        return $response['shelf_id'];
    }

    /**
     * @depends testAdd
     */
    public function testUpdate($shelfId)
    {

        $shelfData = $this->shelf();

        $shelf = new Shelf($this->http);
        $response = $shelf->update(function(ShelfData $shelf) use ($shelfData) {
            return $shelfData;
        }, $shelfId, $this->url,'测试');

        $this->assertTrue($response);
    }

    public function testLists()
    {
        
        $shelf = new Shelf($this->http);
        $response = $shelf->lists();
        $this->assertTrue(is_array($response));
    }

    /**
     * @depends testAdd
     */
    public function testGetById($shelfId)
    {
        
        $shelf = new Shelf($this->http);
        $response = $shelf->getById($shelfId);
        $this->assertTrue(is_array($response));
    }

    /**
     * @depends testAdd
     */
    public function testDelete($shelfId)
    {

        $shelf = new Shelf($this->http);
        $response = $shelf->delete($shelfId);
        $this->assertTrue($response);
    }
}
