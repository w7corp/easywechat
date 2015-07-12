<?php

use EasyWeChat\Core\Application;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Return a normal application.
     *
     * @param array $config
     *
     * @return Application
     */
    public function getApp($config = [])
    {
        $config = array_merge([
            'app_id' => 'overtrue',
            'secret' => 'bar',
            'token' => 'barz',
        ], $config);

        return new Application($config);
    }
}