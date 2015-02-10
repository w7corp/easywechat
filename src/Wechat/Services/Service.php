<?php

namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;

abstract class Service
{
    /**
     * 请求的headers
     *
     * @var array
     */
    protected $headers = array();

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
        return Wechat::request('GET', Wechat::makeUrl($url, $params));
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
        $url = Wechat::makeUrl($url, $queries);

        $headers = array_merge($this->headers, $headers);

        return Wechat::request('POST', $url, $params, $files, $headers);
    }
}