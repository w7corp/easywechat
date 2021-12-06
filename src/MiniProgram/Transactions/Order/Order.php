<?php
/**
 * Created by PhpStorm.
 * User: wangshukai
 * Date: 2021/3/5
 * Time: 4:44 PM
 */

namespace EasyWeChat\MiniProgram\Transactions\Order;

use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class Order extends AbstractMiniProgram
{
    const API_POST_ORDER_SCENE = 'https://api.weixin.qq.com/shop/scene/check';
    const API_POST_ORDER_TICKET = 'https://api.weixin.qq.com/shop/order/add';
    const API_POST_ORDER_PAY = 'https://api.weixin.qq.com/shop/order/pay';
    const API_POST_ORDER_GET = 'https://api.weixin.qq.com/shop/order/get';
    const API_POST_ORDER_GET_LIST = 'https://api.weixin.qq.com/shop/order/get_list';
    const API_POST_ORDER_BY_FINDER = 'https://api.weixin.qq.com/shop/order/get_list_by_finder';
    const API_POST_ORDER_BY_SHARE = 'https://api.weixin.qq.com/shop/order/get_list_by_sharer';


    /** 获取场景
     * @param $scene
     * @return \Psr\Http\Message\StreamInterface
     */
    public function orderScene($scene)
    {
        $params = ["scene" => $scene];
        return $this->getStream(self::API_POST_ORDER_SCENE, $params);
    }

    /** 生成订单并获取ticket
     * @param string $create_time
     * @param int $type
     * @param string $outOrderId
     * @param string $openId
     * @param string $path
     * @param string $outUserId
     * @param array $orderDetail
     * @param array $deliveryDetail
     * @param array $addressInfo
     * @return \Psr\Http\Message\StreamInterface
     */
    public function createOrder(array $params)
    {
        return $this->getStream(self::API_POST_ORDER_TICKET, $params);
    }

    /**按照推广员获取订单
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getListByFinder(array $params)
    {
        return $this->getStream(self::API_POST_ORDER_BY_FINDER, $params);
    }

    /**按照推广员获取订单
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getListByShare(array $params)
    {
        return $this->getStream(self::API_POST_ORDER_BY_SHARE, $params);
    }

    /** 同步支付结果
     * @param string $orderId
     * @param string $outOrderId
     * @param string $openId
     * @param int $actionType
     * @param string $actionRemark
     * @param string $transactionId
     * @param string $payTime
     * @return \Psr\Http\Message\StreamInterface
     */
    public function payOrder(array $params)
    {
        return $this->getStream(self::API_POST_ORDER_PAY, $params);
    }

    /** 获取订单详情
     * @param string $orderId
     * @param string $outOrderId
     * @param string $openId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function orderGet(string $openId, string $orderId = "", string $outOrderId = "")
    {
        $params = [
            "order_id" => $orderId,
            "out_order_id" => $outOrderId,
            "openid" => $openId
        ];
        return $this->getStream(self::API_POST_ORDER_GET, $params);
    }

    /** 获取订单列表
     * @param string $orderId
     * @param string $outOrderId
     * @param string $openId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function orders(array $params)
    {
        return $this->getStream(self::API_POST_ORDER_GET_LIST, $params);
    }

    /**
     * Get stream.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function getStream($endpoint, $params)
    {
        return json_decode(strval($this->getHttp()->json($endpoint, $params)->getBody()), true);
    }
}