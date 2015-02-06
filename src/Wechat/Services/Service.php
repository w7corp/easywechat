<?php

namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;

abstract class Service
{
    /**
     * Wechat实例
     *
     * @var \Overtrue\Wechat\Wechat
     */
    protected $wechat;


    public function __construct(Wechat $wechat)
    {
        $this->wechat = $wechat;

        $this->boot();
    }

    protected function boot()
    {
        # code...
    }

    /**
     * 执行GET请求
     *
     * @param string $url
     * @param array  $params
     *
     * @return array
     */
    public function getRequest($url, $params)
    {
        return $this->wechat->request('GET', $this->wechat->makeUrl($url, $params));
    }

    /**
     * 执行POST请求
     *
     * @param string $url
     * @param array  $params
     * @param array  $queries
     * @param array  $files
     *
     * @return array
     */
    public function postRequest($url, $params, $queries = array(), $files = array())
    {
        return $this->wechat->request('POST', $this->wechat->makeUrl($url, $queries), $params, $files);
    }
}