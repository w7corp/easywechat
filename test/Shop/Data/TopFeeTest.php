<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午5:13
 */
namespace Overtrue\Wechat\Test\Shop\Data;


use Overtrue\Wechat\Shop\Data\TopFee;

class TopFeeTest extends \PHPUnit_Framework_TestCase
{
    public function testSetTopFee()
    {
        $topfee = new TopFee();
        $topfee->setNormal('1','10','1','5')
            ->setCustom('2','10','2','5','浙江省')
            ->setTopFee();


        foreach ($topfee->topFee as $value) {
            $this->assertArrayHasKey('Type',$value);
            $this->assertArrayHasKey('Normal',$value);
            $this->assertArrayHasKey('Custom',$value);
        }

        return $topfee;

    }

    public function testSetCustom()
    {
        $topfee = new TopFee();
        $topfee->setCustom('2','10','2','5','浙江省');

        foreach ($topfee->custom as $value) {
            $this->assertArrayHasKey('StartStandards',$value);
            $this->assertArrayHasKey('StartFees',$value);
            $this->assertArrayHasKey('AddStandards',$value);
            $this->assertArrayHasKey('AddFees',$value);
            $this->assertArrayHasKey('DestCountry',$value);
            $this->assertArrayHasKey('DestProvince',$value);
            $this->assertArrayHasKey('DestCity',$value);
        }

        $topfee = new TopFee();
        $topfee->setCustom('2','10','2','5',array('江苏省','浙江省','上海市'));

        foreach ($topfee->custom as $value) {
            $this->assertArrayHasKey('StartStandards',$value);
            $this->assertArrayHasKey('StartFees',$value);
            $this->assertArrayHasKey('AddStandards',$value);
            $this->assertArrayHasKey('AddFees',$value);
            $this->assertArrayHasKey('DestCountry',$value);
            $this->assertArrayHasKey('DestProvince',$value);
            $this->assertArrayHasKey('DestCity',$value);
        }

        $topfee = new TopFee();
        $topfee->setCustom('2','10','2','5','浙江省',array('杭州市','金华市'));

        foreach ($topfee->custom as $value) {
            $this->assertArrayHasKey('StartStandards',$value);
            $this->assertArrayHasKey('StartFees',$value);
            $this->assertArrayHasKey('AddStandards',$value);
            $this->assertArrayHasKey('AddFees',$value);
            $this->assertArrayHasKey('DestCountry',$value);
            $this->assertArrayHasKey('DestProvince',$value);
            $this->assertArrayHasKey('DestCity',$value);
        }

        $topfee = new TopFee();
        $topfee->setCustom('2','10','2','5','上海市','上海市');

        foreach ($topfee->custom as $value) {
            $this->assertArrayHasKey('StartStandards',$value);
            $this->assertArrayHasKey('StartFees',$value);
            $this->assertArrayHasKey('AddStandards',$value);
            $this->assertArrayHasKey('AddFees',$value);
            $this->assertArrayHasKey('DestCountry',$value);
            $this->assertArrayHasKey('DestProvince',$value);
            $this->assertArrayHasKey('DestCity',$value);
        }
    }

    public function testSetNormal()
    {
        $topfee = new TopFee();
        $topfee->setNormal('1','10','1','5');

        $this->assertArrayHasKey('StartStandards',$topfee->normal);
        $this->assertArrayHasKey('StartFees',$topfee->normal);
        $this->assertArrayHasKey('AddStandards',$topfee->normal);
        $this->assertArrayHasKey('AddFees',$topfee->normal);
    }
}