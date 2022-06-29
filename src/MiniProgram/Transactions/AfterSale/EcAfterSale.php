<?php
/**
 * Created by PhpStorm.
 * User: baixinghai
 * Date: 2022/4/14
 * Time: 17:57 PM
 */

namespace EasyWeChat\MiniProgram\Transactions\AfterSale;

use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

class EcAfterSale extends AbstractMiniProgram
{
    const API_POST_SHOP_ADD = 'https://api.weixin.qq.com/shop/ecaftersale/add';
    const API_POST_SHOP_CANCEL = 'https://api.weixin.qq.com/shop/ecaftersale/cancel';
    const API_POST_SHOP_UPLOAD_RETURNINFO = 'https://api.weixin.qq.com/shop/ecaftersale/uploadreturninfo';
    const API_POST_SHOP_GET = 'https://api.weixin.qq.com/shop/ecaftersale/get';
    const API_POST_SHOP_GET_LIST = 'https://api.weixin.qq.com/shop/ecaftersale/get_list';
    const API_POST_SHOP_ACCEPT_REFUND = 'https://api.weixin.qq.com/shop/ecaftersale/acceptrefund';
    const API_POST_SHOP_ACCEPT_RETURN = 'https://api.weixin.qq.com/shop/ecaftersale/acceptreturn';
    const API_POST_SHOP_REJECT = 'https://api.weixin.qq.com/shop/ecaftersale/reject';
    const API_POST_SHOP_UPLOAD_CERTIFICATES = 'https://api.weixin.qq.com/shop/ecaftersale/upload_certificates';
    const API_POST_SHOP_UPDATE = 'https://api.weixin.qq.com/shop/ecaftersale/update';

    /**
     * 生成售后单
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function add(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_ADD, $params);
    }

    /**
     * 用户取消售后单
     * @param string $outAftersaleId
     * @param int $aftersaleId
     * @param string $openId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function cancel(string $outAftersaleId, int $aftersaleId, string $openId)
    {
        $params = [
            "out_aftersale_id" => $outAftersaleId,
            "aftersale_id" => $aftersaleId,
            "openid" => $openId,
        ];
        // 与out_aftersale_id二选一
        if ($outAftersaleId) {
            unset($params['aftersale_id']);
        }
        return $this->getStream(self::API_POST_SHOP_CANCEL, $params);
    }

    /**
     * 用户上传物流信息
     * @param string $outAftersaleId
     * @param string $aftersaleId
     * @param string $openId
     * @param string $deliveryId
     * @param string $waybillId
     * @param string $deliveryName
     * @return \Psr\Http\Message\StreamInterface
     */
    public function uploadReturnInfo(string $outAftersaleId, string $aftersaleId, string $openId, string $deliveryId, string $waybillId, string $deliveryName)
    {
        $params = [
            "out_aftersale_id" => $outAftersaleId,
            "aftersale_id" => $aftersaleId,
            "openid" => $openId,
            "delivery_id" => $deliveryId,
            "waybill_id" => $waybillId,
            "delivery_name" => $deliveryName,
        ];
        return $this->getStream(self::API_POST_SHOP_UPLOAD_RETURNINFO, $params);
    }

    /**
     * 获取售后单详情
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function get(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_GET, $params);
    }
    
    /**
     * 获取售后单列表
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getList(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_GET_LIST, $params);
    }

    /**
     * 同意退款
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function acceptRefund(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_ACCEPT_REFUND, $params);
    }

    /**
     * 同意退货
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function acceptReturn(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_ACCEPT_RETURN, $params);
    }

    /**
     * 拒绝售后
     * @param string $outAftersaleId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function reject(string $outAftersaleId)
    {
        $params = [
            "out_aftersale_id" => $outAftersaleId,
        ];
        return $this->getStream(self::API_POST_SHOP_REJECT, $params);
    }

    /**
     * 上传退款凭证
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function uploadCertificates(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_UPLOAD_CERTIFICATES, $params);
    }

    /**
     * 更新售后单
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function update(array $params)
    {
        return $this->getStream(self::API_POST_SHOP_UPDATE, $params);
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
        return json_decode(strval($this->getHttp()
            ->json($endpoint, $params)->getBody()), true);
    }
}