<?php
/**
 * Created by PhpStorm.
 * User: wangshukai
 * Date: 2021/3/5
 * Time: 3:18 PM
 */

namespace EasyWeChat\MiniProgram\Transaction\Spu;

use EasyWeChat\Core\Exceptions\HttpException;
use EasyWeChat\MiniProgram\Core\AbstractMiniProgram;

use EasyWeChat\Core\Exceptions\InvalidArgumentException;

class Product extends AbstractMiniProgram
{
    const API_POST_PRODUCT_ADD = 'https://api.weixin.qq.com/shop/spu/add';
    const API_POST_PRODUCT_GET = 'https://api.weixin.qq.com/shop/spu/get';
    const API_POST_PRODUCT_DELETE = 'https://api.weixin.qq.com/shop/spu/del';
    const API_POST_PRODUCT_GET_LIST = 'https://api.weixin.qq.com/shop/spu/get_list';
    const API_POST_PRODUCT_UPDATE = 'https://api.weixin.qq.com/shop/spu/update';
    const API_POST_PRODUCT_NO_CHECK_UPDATE = 'https://api.weixin.qq.com/shop/spu/update_without_audit';
    const API_POST_PRODUCT_UP_TO_STORE = 'https://api.weixin.qq.com/shop/spu/listing';
    const API_POST_PRODUCT_DOWN_TO_STORE = 'https://api.weixin.qq.com/shop/spu/delisting';

    public function addProduct(string $outProductId, string $title, string $path, array $headImg, array $qualificationPics = [],array $descInfo, int $thirdCatId, int $brandId,array $skus)
    {
        $params=[
            "out_product_id"=>$outProductId,
            "title"=>$title,
            "path"=>$path,
            "head_img"=>$headImg,
            "qualification_pics"=>$qualificationPics,
            "desc_info"=>$descInfo,
            "third_cat_id"=>$thirdCatId,
            "brand_id"=>$brandId,
            "skus"=>$skus,
        ];
        return $this->getStream(self::API_POST_PRODUCT_ADD, $params);
    }

    public function delProduct(int $productId=0,string  $out_productId=""){
        $params=[
            "product_id"=>$productId,
            "out_product_id"=>$out_productId,
        ];
        return $this->getStream(self::API_POST_PRODUCT_DELETE, $params);
    }
    public  function getProduct(string  $out_productId="",int $productId=0,int $needEditSpu=0){
        $params=[
            "product_id"=>$productId,
            "out_product_id"=>$out_productId,
            "need_edit_spu"=>$needEditSpu,
        ];
        return $this->getStream(self::API_POST_PRODUCT_GET, $params);
    }
    public  function getProductList(int $status=0,string $startCreateTime="",string $endCreateTime="",string $startUpdateTime="",string  $endUpdateTime="",int $page=1,$pageSize=100,int $needEditSpu=1){
        $params=[
            "status"=>$status,
            "start_create_time"=>$startCreateTime,
            "end_create_time"=>$endCreateTime,
            "start_update_time"=>$startUpdateTime,
            "end_update_time"=>$endUpdateTime,
            "page"=>$page,
            "page_size"=>$pageSize,
            "need_edit_spu"=>$needEditSpu,
        ];
        return $this->getStream(self::API_POST_PRODUCT_GET_LIST, $params);
    }
    public function updateProduct(string $outProductId,int $productId, string $title, string $path, array $headImg, array $qualificationPics = [],array $descInfo, int $thirdCatId, int $brandId,array $skus){
        $params=[
            "out_product_id"=>$outProductId,
            "product_id"=>$productId,
            "title"=>$title,
            "path"=>$path,
            "head_img"=>$headImg,
            "qualification_pics"=>$qualificationPics,
            "desc_info"=>$descInfo,
            "third_cat_id"=>$thirdCatId,
            "brand_id"=>$brandId,
            "skus"=>$skus,
        ];
        return $this->getStream(self::API_POST_PRODUCT_UPDATE, $params);
    }

    public function updateNoCheckProduct(string $outProductId,int $productId,string $path,array $skus){
        $params=[
            "out_product_id"=>$outProductId,
            "product_id"=>$productId,
            "path"=>$path,
            "skus"=>$skus,
        ];
        return $this->getStream(self::API_POST_PRODUCT_NO_CHECK_UPDATE, $params);
    }
    public function upToStoreProduct(int $productId=0,string  $out_productId=""){
        $params=[
            "product_id"=>$productId,
            "out_product_id"=>$out_productId,
        ];
        return $this->getStream(self::API_POST_PRODUCT_UP_TO_STORE, $params);
    }
    public function downToStoreProduct(int $productId=0,string  $out_productId=""){
        $params=[
            "product_id"=>$productId,
            "out_product_id"=>$out_productId,
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
        return json_decode(strval($this->getHttp()->json($endpoint, $params)->getBody()),true);
    }
}