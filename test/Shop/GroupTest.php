<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午3:18
 */
namespace Overtrue\Wechat\Test\Shop;


use Overtrue\Wechat\Shop\Group;
use Overtrue\Wechat\Test\TestBase;

class GroupTest extends TestBase
{

    private $productId = 'pe4OowbHVULEWICFN1t6iy2BPWXA';

    /**
     * @var Group
     */
    protected $group;

    protected function setUp()
    {
        parent::setUp();

        $this->group = new Group($this->http);
    }

    public function testAdd()
    {
        $response = $this->group->add('分组名',array($this->productId));
        $this->assertTrue(is_numeric($response));

        return $response;
    }

    /**
     * @depends testAdd
     */
    public function testUpdateAttribute($groupId)
    {
        $response = $this->group->updateAttribute($groupId,'分组名');
        $this->assertTrue($response);
    }

    /**
     * @depends testAdd
     */
    public function testUpdateProduct($groupId)
    {
        $response = $this->group->updateProduct($groupId,array(array($this->productId,0)));
        $this->assertTrue($response);

    }

    public function testLists()
    {
        $response = $this->group->lists();
        $this->assertTrue(is_array($response));
    }

    public function testGetById()
    {
        $groupId = '207133170';

        $response = $this->group->getById($groupId);
        $this->assertTrue(is_array($response));
    }

    /**
     * @depends testAdd
     */
    public function testDelete($groupId)
    {
        $response = $this->group->delete($groupId);
        $this->assertTrue($response);

    }

}
