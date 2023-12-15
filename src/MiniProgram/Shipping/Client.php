<?php

namespace EasyWeChat\MiniProgram\Shipping;

use EasyWeChat\Kernel\BaseClient;

class Client extends BaseClient
{
    /**
     * 发货信息录入接口
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadShippingInfo(array $params)
    {
        return $this->httpPostJson('wxa/sec/order/upload_shipping_info', $params);
    }

    /**
     * 发货信息合单录入接口
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function uploadCombineShippingInfo(array $params)
    {
        return $this->httpPostJson('wxa/sec/order/upload_combined_shipping_info', $params);
    }


    /**
     * 查询订单发货状态
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrder(array $params)
    {
        return $this->httpPostJson('wxa/sec/order/get_order', $params);
    }

    /**
     * 查询订单列表
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOrderList(array $params = [])
    {
        return $this->httpPostJson('wxa/sec/order/get_order_list', $params);
    }

    /**
     * 确认收货提醒接口
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function notifyConfirmReceive(array $params)
    {
        return $this->httpPostJson('wxa/sec/order/notify_confirm_receive', $params);
    }

    /**
     * 消息跳转路径设置接口
     *
     * @param string $path
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setMsgJumpPath(string $path)
    {
        $params = [
            'path' => $path
        ];

        return $this->httpPostJson('wxa/sec/order/set_msg_jump_path', $params);
    }

    /**
     * 查询小程查询小程序是否已开通发货信息管理服务
     *
     * @param string $appID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function isTradeManaged(string $appID = '')
    {
        $params = [
            'appid' => empty($appID) ? $this->app['config']['app_id'] : $appID
        ];

        return $this->httpPostJson('wxa/sec/order/is_trade_managed', $params);
    }

    /**
     * 查询小程序是否已完成交易结算管理确认
     *
     * @param string $appID
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function isTradeCompleted(string $appID = '')
    {
        $params = [
            'appid' => empty($appID) ? $this->app['config']['app_id'] : $appID
        ];

        return $this->httpPostJson('wxa/sec/order/is_trade_management_confirmation_completed', $params);
    }
}
