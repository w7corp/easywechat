<?php

namespace EasyWeChat\MiniProgram\Shop\Aftersale;

use EasyWeChat\Kernel\BaseClient;

/**
 * 自定义版交易组件及开放接口 - 售后接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Aftersale
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class Client extends BaseClient
{
    /**
     * 创建售后
     *
     * @param array $aftersale
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(array $aftersale)
    {
        return $this->httpPostJson('shop/ecaftersale/add', $aftersale);
    }

    /**
     * 获取订单下售后单
     *
     * @param array $order 订单数据
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(array $order)
    {
        return $this->httpPostJson('shop/ecaftersale/get', $order);
    }

    /**
     * 更新售后
     *
     * @param array $order 订单数据
     * @param array $aftersale 售后数据
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(array $order, array $aftersale)
    {
        return $this->httpPostJson('shop/ecaftersale/update', array_merge($order, $aftersale));
    }
}
