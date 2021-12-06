<?php
/**
 * Created by PhpStorm.
 * User: wangshukai
 * Date: 2021/3/5
 * Time: 3:18 PM
 */

namespace EasyWeChat\MiniProgram\Transactions\Spu;

use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class Product extends AbstractMiniProgram
{
    const API_POST_PRODUCT_ADD = 'https://api.weixin.qq.com/shop/spu/add';
    const API_POST_PRODUCT_DEL_AUDIT = 'https://api.weixin.qq.com/shop/spu/del_audit';
    const API_POST_PRODUCT_GET = 'https://api.weixin.qq.com/shop/spu/get';
    const API_POST_PRODUCT_DELETE = 'https://api.weixin.qq.com/shop/spu/del';
    const API_POST_PRODUCT_GET_LIST = 'https://api.weixin.qq.com/shop/spu/get_list';
    const API_POST_PRODUCT_UPDATE = 'https://api.weixin.qq.com/shop/spu/update';
    const API_POST_PRODUCT_NO_CHECK_UPDATE = 'https://api.weixin.qq.com/shop/spu/update_without_audit';
    const API_POST_PRODUCT_UP_TO_STORE = 'https://api.weixin.qq.com/shop/spu/listing';
    const API_POST_PRODUCT_DOWN_TO_STORE = 'https://api.weixin.qq.com/shop/spu/delisting';

    /**创建视频号商品
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function addProduct(array $params)
    {
        return $this->getStream(self::API_POST_PRODUCT_ADD, $params);
    }

    /** 删除视频号商品
     * @param int $productId
     * @param string $out_productId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function delProduct(int $productId = 0, string $out_productId = "")
    {
        $params = [
            "product_id" => $productId,
            "out_product_id" => $out_productId,
        ];
        return $this->getStream(self::API_POST_PRODUCT_DELETE, $params);
    }

    /**撤回商品审核
     * @param int $productId
     * @param string $out_productId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function delAudit(int $productId = 0, string $out_productId = "")
    {
        $params = [
            "product_id" => $productId,
            "out_product_id" => $out_productId,
        ];
        return $this->getStream(self::API_POST_PRODUCT_DEL_AUDIT, $params);
    }

    /** 获取视频号商品详情
     * @param string $out_productId
     * @param int $productId
     * @param int $needEditSpu
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getProduct(string $out_productId = "", int $productId = 0, int $needEditSpu = 0)
    {
        $params = [
            "product_id" => $productId,
            "out_product_id" => $out_productId,
            "need_edit_spu" => $needEditSpu,
        ];
        return $this->getStream(self::API_POST_PRODUCT_GET, $params);
    }

    /**获取商品列表
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getProducts(array $params)
    {
        return $this->getStream(self::API_POST_PRODUCT_GET_LIST, $params);
    }

    /** 更新视频号详情
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function updateProduct(array $params)
    {
        return $this->getStream(self::API_POST_PRODUCT_UPDATE, $params);
    }

    /**免审更新
     * @param string $outProductId
     * @param int $productId
     * @param string $path
     * @param array $skus
     * @return \Psr\Http\Message\StreamInterface
     */
    public function updateNoCheckProduct(string $outProductId, int $productId, string $path, array $skus)
    {
        $params = [
            "out_product_id" => $outProductId,
            "product_id" => $productId,
            "path" => $path,
            "skus" => $skus,
        ];
        return $this->getStream(self::API_POST_PRODUCT_NO_CHECK_UPDATE, $params);
    }

    /**上架商品
     * @param int $productId
     * @param string $out_productId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function upToStoreProduct(int $productId = 0, string $out_productId = "")
    {
        $params = [
            "product_id" => $productId,
            "out_product_id" => $out_productId,
        ];
        return $this->getStream(self::API_POST_PRODUCT_UP_TO_STORE, $params);
    }

    /**下架商品
     * @param int $productId
     * @param string $out_productId
     * @return \Psr\Http\Message\StreamInterface
     */
    public function downToStoreProduct(int $productId = 0, string $out_productId = "")
    {
        $params = [
            "product_id" => $productId,
            "out_product_id" => $out_productId,
        ];
        return $this->getStream(self::API_POST_PRODUCT_DOWN_TO_STORE, $params);
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