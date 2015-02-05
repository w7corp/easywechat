<?php

namespace Overtrue\Wechat\Services;

use Overtrue\Wechat\Wechat;

abstract class Service
{
    /**
     * Wechat实例
     *
     * @var Overtrue\Wechat\Wechat;
     */
    protected $wechat;


    public function __construct(Wechat $wechat)
    {
        $this->wechat = $wechat;
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

    /**
     * 发起一个HTTP/HTTPS的请求
     *
     * @param string $method 请求类型   GET | POST
     * @param string $url    接口的URL
     * @param array  $params 接口参数
     * @param array  $files  图片信息
     *
     * @return array
     */
    public function request($method, $url, array $params = array(), array $files = array())
    {
        $response = Http::request($method, $url, $params, array(), $files);

        if (empty($response)) {
            throw new Exception("服务器无响应");
        }

        $contents = json_decode($response, true);

        if(!empty($contents['errcode'])){
            throw new Exception("[{$contents['errcode']}] ".$contents['errmsg'], $contents['errcode']);
        }

        return $contents;
    }
}