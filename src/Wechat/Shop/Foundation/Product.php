<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Product.php.
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    a939638621 <a939638621@hotmail.com>
 * @copyright 2015 a939638621 <a939638621@hotmail.com>
 *
 * @link      https://github.com/a939638621
 */

namespace Overtrue\Wechat\Shop\Foundation;

/**
 * 商品
 *
 * Interface Product
 */
interface Product
{
    /**
     * 查询商品
     *
     * @param array $data
     *
     * @return mixed
     */
    public function create($data);

    /**
     * 删除商品
     *
     * @param $productId
     *
     * @return mixed
     */
    public function delete($productId);

    /**
     * 修改商品
     *
     * @param $productId
     * @param $data
     * @param bool|false $shelf
     *
     * @return mixed
     */
    public function update($productId, $data, $shelf = false);

    /**
     * 查询商品
     *
     * @param $productId
     *
     * @return mixed
     */
    public function get($productId);

    /**
     * 从状态获取商品
     *
     * @param $status
     *
     * @return mixed
     */
    public function getByStatus($status = 0);

    /**
     * 商品上下架.
     *
     * @param $productId
     * @param int $status
     *
     * @return mixed
     */
    public function updateStatus($productId, $status = 0);

    /**
     * 获取指定分类的所有子分类.
     *
     * @param $cateId
     *
     * @return mixed
     */
    public function getSub($cateId = 1);

    /**
     * 获取指定子分类的所有SKU.
     *
     * @param $cateId
     *
     * @return mixed
     */
    public function getSku($cateId);

    /**
     * 获取指定分类的所有属性.
     *
     * @param $cateId
     *
     * @return mixed
     */
    public function getProperty($cateId);
}
