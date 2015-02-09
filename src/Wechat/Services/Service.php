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

    /**
     * 请求的headers
     *
     * @var array
     */
    protected $headers;


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
    public function getRequest($url, $params = array())
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
     * @param array  $headers
     *
     * @return array
     */
    public function postRequest($url, $params = array(), $queries = array(), $files = array(), $headers = array())
    {
        $url = $this->wechat->makeUrl($url, $queries);

        $headers = array_merge($this->headers, $headers);

        return $this->wechat->request('POST', $url, $params, $files, $headers);
    }
}