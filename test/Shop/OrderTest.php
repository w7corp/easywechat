<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午4:51
 */
namespace Overtrue\Wechat\Test\Shop;


use Overtrue\Wechat\Shop\Order;
use Overtrue\Wechat\Test\TestBase;

class OrderTest extends TestBase
{
    /**
     * @var Order
     */
    protected $order;

    protected function setUp()
    {
        parent::setUp();

        $this->order = new Order($this->http);
    }

    public function testGetById()
    {
        $orderId = '13954548010111875781';

        $response = $this->order->getById($orderId);

        $this->assertTrue(is_array($response));

        return $orderId;

    }


    public function testGetByAttribute()
    {
        $response = $this->order->getByAttribute(2,time()-1000,time());

        $this->assertTrue(is_array($response));

        $response = $this->order->getByAttribute();

        $this->assertTrue(is_array($response));

        return $response;

    }

    /**
     * @depends testGetById
     */
    public function testSetDelivery($orderId)
    {

        /**
         *  setDelivery($orderId,$deliveryCompany = null,$deliveryTrackNo = null,$isOthers = 0)
         */
        $response = $this->order->setDelivery($orderId);
        $this->assertTrue($response);
    }

    /**
     * @depends testGetById
     */
    public function testClose($orderId)
    {
        $response = $this->order->close($orderId);
        $this->assertTrue($response);
    }
}
