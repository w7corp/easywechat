<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyWeChat\Work\ExternalContact;

use EasyWeChat\Kernel\BaseClient;

/**
 * Class ProductClient.
 *
 * @package EasyWeChat\Work\ExternalContact
 *
 * @author 读心印 <aa24615@qq.com>
 */
class ProductClient extends BaseClient
{
    /**
     * 创建商品图册.
     *
     * @see https://developer.work.weixin.qq.com/document/path/95096
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @author 读心印 <aa24615@qq.com>
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function createProductAlbum(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/add_product_album', $params);
    }

    /**
     * 获取商品图册.
     *
     * @see https://developer.work.weixin.qq.com/document/path/95096
     *
     * @param string $productId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @author 读心印 <aa24615@qq.com>
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getProductAlbumDetails(string $productId)
    {
        $params = [
            'product_id' => $productId
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_product_album', $params);
    }

    /**
     * 获取商品图册列表.
     *
     * @see https://developer.work.weixin.qq.com/document/path/95096
     *
     * @param int $limit
     * @param string $cursor
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @author 读心印 <aa24615@qq.com>
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getProductAlbums(int $limit, string $cursor)
    {
        $params = [
            'limit' => $limit,
            'cursor' => $cursor,
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/get_product_album_list', $params);
    }

    /**
     * 编辑商品图册.
     *
     * @see https://developer.work.weixin.qq.com/document/path/95096
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @author 读心印 <aa24615@qq.com>
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function updateProductAlbum(array $params)
    {
        return $this->httpPostJson('cgi-bin/externalcontact/update_product_album', $params);
    }

    /**
     * 删除商品图册.
     *
     * @see https://developer.work.weixin.qq.com/document/path/95096
     *
     * @param string $productId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @author 读心印 <aa24615@qq.com>
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteProductAlbum(string $productId)
    {
        $params = [
            'product_id' => $productId
        ];

        return $this->httpPostJson('cgi-bin/externalcontact/delete_product_album', $params);
    }
}
