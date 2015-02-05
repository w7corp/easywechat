<?php

class TestCase extends PHPUnit_Framework_TestCase
{
    protected $wechat;

    protected function setUp()
    {
        $this->wechat = self::mock('Overtrue\Wechat\Wechat');
    }

    protected function mock($class)
    {
        return \Mockery::mock($class);
    }

    protected function tearDown()
    {
        $this->wechat = NULL;
        \Mockery::close();
    }
}
