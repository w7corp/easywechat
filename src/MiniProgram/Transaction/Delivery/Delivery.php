<?php
/**
 * Created by PhpStorm.
 * User: wangshukai
 * Date: 2021/3/5
 * Time: 5:38 PM
 */

namespace EasyWeChat\MiniProgram\Transaction\Delivery;

use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class Delivery extends AbstractMiniProgram
{

    const API_POST_DELIVERY_LIST = 'https://api.weixin.qq.com/shop/delivery/get_company_list';
    const API_POST_DELIVERY_TAKE = 'https://api.weixin.qq.com/shop/delivery/recieve';
    const API_POST_DELIVERY_SEND = 'https://api.weixin.qq.com/shop/delivery/send';


    /**获取快递公司信息
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getCompanyList()
    {
        $params = [];
        return $this->getStream(self::API_POST_DELIVERY_LIST, $params);
    }

    /** 订单收货
     * @param string $orderId
     * @param string $outOrderId
     * @param string $openId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function recieve(string $orderId, string $outOrderId, string $openId)
    {
        $params = [
            "order_id" => $orderId,
            "out_order_id" => $outOrderId,
            "openid" => $openId,
        ];
        return $this->getStream(self::API_POST_DELIVERY_TAKE, $params);
    }

    public function send(string $orderId, string $outOrderId, string $openId, int $finishAllDelivery, array $deliveryList)
    {
        $params = [
            "order_id" => $orderId,
            "out_order_id" => $outOrderId,
            "openid" => $openId,
            "finish_all_delivery" => $finishAllDelivery,
            "delivery_list" => $deliveryList,
        ];
        return $this->getStream(self::API_POST_DELIVERY_SEND, $params);
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
        return json_decode(strval($this->getHttp()->json($endpoint, $params)->getBody()),true);
    }
}