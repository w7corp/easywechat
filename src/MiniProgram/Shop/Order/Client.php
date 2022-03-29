<?php

namespace EasyWeChat\MiniProgram\Shop\Order;

use EasyWeChat\Kernel\BaseClient;

/**
 * 自定义版交易组件及开放接口 - 订单接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Order
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class Client extends BaseClient
{
    /**
     * 检查场景值是否在支付校验范围内
     *
     * @param int $scene 场景值
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sceneCheck(int $scene)
    {
        return $this->httpPostJson('shop/scene/check', ['scene' => $scene]);
    }

    /**
     * 生成订单
     *
     * @param array $order
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $order)
    {
        return $this->httpPostJson('shop/order/add', $order);
    }

    /**
     * 生成订单
     *
     * @param array $order
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(array $order)
    {
        return $this->httpPostJson('shop/order/add', $order);
    }

    /**
     * 获取订单详情
     *
     * @param string $openid 用户的openid
     * @param array $orderId 微信侧订单id （订单id二选一）
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $openid, array $orderId)
    {
        return $this->httpPostJson('shop/order/get', array_merge($orderId, ['openid' => $openid]));
    }

    /**
     * 关闭订单
     *
     * @param array $params 请求参数
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function close(array $params)
    {
        return $this->httpPostJson('shop/order/close', $params);
    }

    /**
     * 获取订单列表
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList(array $data)
    {
        return $this->httpPostJson('shop/order/get_list', $data);
    }

    /**
     * 同步订单支付结果
     *
     * @param array $pay
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pay(array $pay)
    {
        return $this->httpPostJson('shop/order/pay', $pay);
    }

    /**
     * 同步订单支付结果
     *
     * @param array $pay
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncPayState(array $pay)
    {
        return $this->httpPostJson('shop/order/pay', $pay);
    }

    /**
     * 同步订单支付结果
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPayInfo(array $params)
    {
        return $this->httpPostJson('shop/order/getpaymentparams', $params);
    }

    /**
     * 获取推广员订单
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getFinderOrders(array $params)
    {
        return $this->httpPostJson('shop/order/get_list_by_finder', $params);
    }

    /**
     * 获取分享员订单
     *
     * @param array $params
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSharerOrders(array $params)
    {
        return $this->httpPostJson('shop/order/get_list_by_sharer', $params);
    }
}
