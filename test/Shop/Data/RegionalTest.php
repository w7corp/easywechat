<?php

/**
 * Created by PhpStorm.
 * User: pjxh
 * Date: 15-11-1
 * Time: 下午5:12
 */
namespace Test\Data;


use Overtrue\Wechat\Shop\Data\Regional;

class RegionalTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCountry()
    {
        $regional = new Regional();
        $data = $regional->getCountry();
        $this->assertEquals(array('中国'),$data);
    }

    public function testGetProvince()
    {
        $regional = new Regional();
        $data = $regional->getProvince();
        $this->assertTrue(is_array($data));
    }

    public function testGetCity()
    {
        $regional = new Regional();
        $data = $regional->getCity('上海市');
        $this->assertEquals(array(array('上海市')),$data);

        $regional = new Regional();
        $data = $regional->getCity('北京市');
        $this->assertEquals(array(array('北京市')),$data);

        return $data;
    }
}
