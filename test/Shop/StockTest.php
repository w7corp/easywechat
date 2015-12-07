<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午5:11
 */
namespace Overtrue\Wechat\Test\Shop;


use Overtrue\Wechat\Shop\Stock;
use Overtrue\Wechat\Test\TestBase;

class StockTest extends TestBase
{
//    /**
//     * @depends ProductTest::testCreate
//     * @depends testGetSkuInfo
//     */
    public function testAdd()
    {
        $productId = 'pe4OowbHVULEWICFN1t6iy2BPWXA';
        $skuInfo = '';
        
        $stock = new Stock($this->http);
        $response = $stock->add($productId,100);
        $this->assertTrue($response);

        $stock = new Stock($this->http);
        $response = $stock->add($productId,'100');
        $this->assertTrue($response);
    }

//    /**
//     * @depends ProductTest::testCreate
//     * @depends testGetSkuInfo
//     */
    public function testReduce()
    {

        $productId = 'pe4OowbHVULEWICFN1t6iy2BPWXA';
        $skuInfo = '';
        
        $stock = new Stock($this->http);
        $response = $stock->reduce($productId,100);
        $this->assertTrue($response);

        $stock = new Stock($this->http);
        $response = $stock->reduce($productId,100);
        $this->assertTrue($response);
    }

    public function testGetSkuInfo()
    {
        
        $stock = new Stock($this->http);
        $data = $stock->getSkuInfo(array(array('a','a1'),array('b','b1')));
        $this->assertEquals('a:a1;b:b1',$data);
    }
}
