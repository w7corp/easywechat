<?php

namespace EasyWeChat\MiniProgram\Shop\Spu;

use EasyWeChat\Kernel\BaseClient;

/**
 * 自定义版交易组件及开放接口 - SPU接口
 *
 * @package EasyWeChat\MiniProgram\Shop\Spu
 * @author HaoLiang <haoliang@qiyuankeji.cn>
 */
class Client extends BaseClient
{
    /**
     * 添加商品
     *
     * @param array $product 商品信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add(array $product)
    {
        return $this->httpPostJson('shop/spu/add', $product);
    }

    /**
     * 删除商品
     *
     * @param array $productId 商品编号信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function del(array $productId)
    {
        return $this->httpPostJson('shop/spu/del', $productId);
    }

    /**
     * 获取商品
     *
     * @param array $productId 商品编号信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(array $productId)
    {
        return $this->httpPostJson('shop/spu/get', $productId);
    }

    /**
     * 获取商品列表
     *
     * @param array $product 商品信息
     * @param array $page 分页信息
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getList(array $product, array $page)
    {
        return $this->httpPostJson('shop/spu/get_list', array_merge($product, $page));
    }

    /**
     * 撤回商品审核
     *
     * @param array $productId 商品编号信息 交易组件平台内部商品ID，与out_product_id二选一
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delAudit(array $productId)
    {
        return $this->httpPostJson('shop/spu/del_audit', $productId);
    }

    /**
     * 更新商品
     *
     * @param array $product
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(array $product)
    {
        return $this->httpPostJson('shop/spu/update', $product);
    }

    /**
     * 该免审更新商品
     *
     * @param array $product
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateWithoutAudit(array $product)
    {
        return $this->httpPostJson('shop/spu/update_without_audit', $product);
    }

    /**
     * 上架商品
     *
     * @param array $productId 商品编号数据 交易组件平台内部商品ID，与out_product_id二选一
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listing(array $productId)
    {
        return $this->httpPostJson('shop/spu/listing', $productId);
    }

    /**
     * 下架商品
     *
     * @param array $productId
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delisting(array $productId)
    {
        return $this->httpPostJson('shop/spu/delisting', $productId);
    }
}
