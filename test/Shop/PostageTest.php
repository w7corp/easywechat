<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午5:10
 */
namespace Overtrue\Wechat\Test\Shop;


use Overtrue\Wechat\Shop\Postage;
use Overtrue\Wechat\Shop\Data\TopFee;
use Overtrue\Wechat\Test\TestBase;

class PostageTest extends TestBase
{

    public function topfee($arry = false)
    {
        $topfee = new TopFee();
        $topfee->setNormal('1','10','1','5')
            ->setCustom('2','10','2','5','浙江省')
            ->setTopFee();

        if ($arry) $topfee->toArray();

        return $topfee;
    }


    public function testAdd()
    {
        $topfee = $this->topfee();

        $postage = new Postage($this->http);
        $response[0] = $postage->add('test1',function(TopFee $topFeedata) use ($topfee) {
            return $topfee;
        },0,0);

        $this->assertTrue(is_numeric($response[0]));

        $postage = new Postage($this->http);
        $response[1] = $postage->add('test2',$this->topfee(true),0,0);

        $this->assertTrue(is_numeric($response[1]));

        return $response;
    }

    /**
     * @depends testAdd
     */
    public function testDelete($templateId)
    {
        
        $postage = new Postage($this->http);
        $response = $postage->delete($templateId[0]);
        $this->assertTrue($response);
    }


    /**
     * @depends testAdd
     */
    public function testUpdate($templateId)
    {
        $topfee  = $this->topfee();

        $postage = new Postage($this->http);
        $response = $postage->update($templateId[1],'test',function(TopFee $topFeeData) use ($topfee){
            return $topfee;
        },0,0);

        $this->assertTrue($response);

        $postage = new Postage($this->http);
        $response = $postage->update($templateId[1],'test',$this->topfee(true),0,0);

        $this->assertTrue($response);
    }

    /**
     * @depends testAdd
     */
    public function testGetById($templateId)
    {
        
        $postage = new Postage($this->http);
        $response = $postage->getById($templateId[1]);
        $this->assertTrue(is_array($response));
    }

    public function testLists()
    {
        
        $postage = new Postage($this->http);
        $response = $postage->lists();
        $this->assertTrue(is_array($response));
    }

}
